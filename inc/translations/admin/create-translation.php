<?php

/**
 * "Create translation" — duplicate a post into another language and AI-translate it.
 *
 * Adds a Translations meta box to the post/page editor that lists every
 * language. For languages without a translation yet, a button duplicates the
 * current post into a new draft, links it to the same translation group, and
 * runs the title + block text through the AI translator.
 *
 * Translates monolingual block content:
 *   - Snel blocks → their text attributes (see snel_block_text_attrs).
 *   - Core blocks (paragraph, heading, list…) → their inner text.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

// Meta key on a translation storing the source signature it was last built from.
if (! defined('SNEL_SRC_HASH_META')) {
    define('SNEL_SRC_HASH_META', '_snel_src_hash');
}

/**
 * Register the AJAX handlers and expose per-post translation data to the editor.
 */
function snel_create_translation_register(): void
{
    add_action('wp_ajax_snel_create_translation', 'snel_create_translation_ajax');
    add_action('wp_ajax_snel_sync_translation', 'snel_sync_translation_ajax');
    add_action('wp_ajax_snel_translation_state', 'snel_translation_state_ajax');
    add_action('enqueue_block_editor_assets', 'snel_create_translation_editor_data', 20);
}
add_action('init', 'snel_create_translation_register');

/**
 * Localize the current post's translation state for the Snel Stack editor
 * sidebar: which language it's in, which siblings exist, and the nonce/url to
 * create new ones.
 */
function snel_create_translation_editor_data(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || $screen->base !== 'post') {
        return;
    }

    $post = get_post();
    if (! $post) {
        return;
    }

    wp_localize_script('snel-editor-snelstack', 'snelCreateTranslation', [
        'ajaxUrl'     => admin_url('admin-ajax.php'),
        'nonce'       => wp_create_nonce('snel_create_translation'),
        'postId'      => $post->ID,
        'currentLang' => snel_post_lang($post->ID),
        'defaultLang' => snel_get_default_lang(),
        'languages'   => snel_translation_languages($post->ID),
    ]);
}

/**
 * Build the per-language translation state for a post: which languages exist,
 * their edit/view URLs, publish status, and whether each is out of date.
 *
 * @return array
 */
function snel_translation_languages(int $post_id): array
{
    $config       = snel_get_languages_config();
    $this_lang    = snel_post_lang($post_id);
    $siblings     = snel_get_translations($post_id);
    $default_lang = snel_get_default_lang();

    // Signature of the group's source (default-language) post, used to flag
    // translations that fell out of date after a later source edit.
    $source_id  = snel_source_post_id($post_id);
    $source_sig = $source_id ? snel_source_signature($source_id) : '';

    $languages = [];
    foreach (snel_get_supported_langs() as $code) {
        $sib = $siblings[$code] ?? 0;

        // Out of date only if we have a stored signature that differs. Legacy
        // translations without one are left un-flagged (unknown, not stale).
        $outdated = false;
        if ($sib && $code !== $default_lang && $source_sig) {
            $stored   = get_post_meta($sib, SNEL_SRC_HASH_META, true);
            $outdated = ($stored !== '' && $stored !== $source_sig);
        }

        $languages[] = [
            'code'      => $code,
            'label'     => $config[$code]['label'] ?? strtoupper($code),
            'isCurrent' => $code === $this_lang,
            'postId'    => $sib ?: null,
            'editUrl'   => $sib ? get_edit_post_link($sib, 'raw') : null,
            'viewUrl'   => $sib ? get_permalink($sib) : null,
            'status'    => $sib ? get_post_status($sib) : null,
            'outdated'  => $outdated,
        ];
    }

    return $languages;
}

/**
 * AJAX: return the current translation state for a post (used to refresh the
 * editor sidebar after a save, without a full page reload).
 */
function snel_translation_state_ajax(): void
{
    check_ajax_referer('snel_create_translation', 'nonce');

    $post_id = (int) ($_POST['post_id'] ?? 0);
    if (! $post_id || ! current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    wp_send_json_success([
        'currentLang' => snel_post_lang($post_id),
        'defaultLang' => snel_get_default_lang(),
        'languages'   => snel_translation_languages($post_id),
    ]);
}

/**
 * AJAX: duplicate a post into a target language and AI-translate it.
 */
function snel_create_translation_ajax(): void
{
    check_ajax_referer('snel_create_translation', 'nonce');

    $source_id = (int) ($_POST['post_id'] ?? 0);
    $target    = sanitize_text_field(wp_unslash($_POST['target'] ?? ''));

    if (! $source_id || ! current_user_can('edit_post', $source_id)) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }
    if (! in_array($target, snel_get_supported_langs(), true)) {
        wp_send_json_error(['message' => 'Invalid language']);
    }

    $source = get_post($source_id);
    if (! $source) {
        wp_send_json_error(['message' => 'Source post not found']);
    }

    $source_lang = snel_post_lang($source_id);
    $group       = snel_post_group($source_id);

    // If a translation already exists, just open it.
    $existing = snel_get_translation($source_id, $target);
    if ($existing) {
        wp_send_json_success(['edit_url' => get_edit_post_link($existing, 'raw'), 'post_id' => $existing, 'existed' => true]);
    }

    // Translate title + excerpt + block content.
    $tr = snel_translate_source_fields($source, $source_lang, $target);
    if (is_wp_error($tr)) {
        wp_send_json_error(['message' => $tr->get_error_message()]);
    }
    $new_title   = $tr['title'];
    $new_excerpt = $tr['excerpt'];
    $new_content = $tr['content'];

    // Map the parent to its translation if one exists.
    $new_parent = $source->post_parent;
    if ($source->post_parent) {
        $parent_sibling = snel_get_translation($source->post_parent, $target);
        if ($parent_sibling) {
            $new_parent = $parent_sibling;
        }
    }

    $new_id = wp_insert_post([
        'post_type'      => $source->post_type,
        'post_status'    => 'draft',
        'post_title'     => $new_title,
        'post_content'   => $new_content,
        'post_excerpt'   => $new_excerpt,
        'post_parent'    => $new_parent,
        'menu_order'     => $source->menu_order,
        'comment_status' => $source->comment_status,
        'ping_status'    => $source->ping_status,
    ], true);

    if (is_wp_error($new_id)) {
        wp_send_json_error(['message' => $new_id->get_error_message()]);
    }

    // Link both posts into the same translation group.
    TranslationGroup::link($source_id, $group, $source_lang);
    TranslationGroup::link($new_id, $group, $target);

    // Copy over relevant meta (featured image, page template, etc.).
    snel_copy_translatable_meta($source_id, $new_id);

    // AI-translate declared text meta keys (ACF text fields etc.) in place.
    snel_translate_post_meta($source_id, $new_id, $source_lang, $target);

    // Record the source signature so we can later detect this translation
    // going out of date (source edited afterwards).
    update_post_meta($new_id, SNEL_SRC_HASH_META, snel_source_signature($source_id));

    wp_send_json_success([
        'edit_url' => get_edit_post_link($new_id, 'raw'),
        'post_id'  => $new_id,
    ]);
}

/**
 * Translate a source post's title, excerpt and block content into a target
 * language. Shared by the create and sync flows.
 *
 * @return array|WP_Error ['title' => ..., 'excerpt' => ..., 'content' => ...]
 */
function snel_translate_source_fields(WP_Post $source, string $source_lang, string $target)
{
    $meta_texts = [$source->post_title];
    if ($source->post_excerpt !== '') {
        $meta_texts[] = $source->post_excerpt;
    }
    $meta_tr = snel_ai_translate($meta_texts, $source_lang, $target);
    if (is_wp_error($meta_tr)) {
        return $meta_tr;
    }

    $content = snel_translate_block_content($source->post_content, $source_lang, $target);
    if (is_wp_error($content)) {
        return $content;
    }

    return [
        'title'   => $meta_tr[0] ?? $source->post_title,
        'excerpt' => ($source->post_excerpt !== '' && isset($meta_tr[1])) ? $meta_tr[1] : '',
        'content' => $content,
    ];
}

/**
 * A signature of everything that gets translated on a source post (title,
 * excerpt, block text, declared meta). Stored on each translation at create/
 * sync time; if the source's signature later differs, the translation is stale.
 * Ignores non-translated fields so unrelated edits don't raise false flags.
 */
function snel_source_signature(int $source_id): string
{
    $post = get_post($source_id);
    if (! $post) {
        return '';
    }

    $parts = [$post->post_title, $post->post_excerpt];

    $strings = [];
    snel_blocks_collect_strings(parse_blocks($post->post_content), $strings);
    $parts = array_merge($parts, $strings);

    foreach (snel_translatable_meta_keys($post->post_type) as $key) {
        $val = get_post_meta($source_id, $key, true);
        if (is_string($val)) {
            $parts[] = $val;
        }
    }

    return md5(implode("\x1f", $parts));
}

/**
 * The default-language (source) post id for whatever post is given.
 */
function snel_source_post_id(int $post_id): int
{
    if (snel_post_lang($post_id) === snel_get_default_lang()) {
        return $post_id;
    }
    return (int) snel_get_translation($post_id, snel_get_default_lang());
}

/**
 * AJAX: re-translate the source into an existing translation, overwriting its
 * title, excerpt, block content and declared text meta. Target-only meta
 * (featured image, template) is left untouched. Resets the stored signature so
 * the translation reads as up to date again.
 */
function snel_sync_translation_ajax(): void
{
    check_ajax_referer('snel_create_translation', 'nonce');

    $post_id = (int) ($_POST['post_id'] ?? 0);
    $target  = sanitize_text_field(wp_unslash($_POST['target'] ?? ''));

    if (! $post_id || ! current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }
    if (! in_array($target, snel_get_supported_langs(), true)) {
        wp_send_json_error(['message' => 'Invalid language']);
    }

    $source_id = snel_source_post_id($post_id);
    $source    = $source_id ? get_post($source_id) : null;
    if (! $source) {
        wp_send_json_error(['message' => 'Source post not found']);
    }

    $target_id = (int) snel_get_translation($source_id, $target);
    if (! $target_id) {
        wp_send_json_error(['message' => 'Translation does not exist']);
    }
    if (! current_user_can('edit_post', $target_id)) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    $source_lang = snel_post_lang($source_id);

    $tr = snel_translate_source_fields($source, $source_lang, $target);
    if (is_wp_error($tr)) {
        wp_send_json_error(['message' => $tr->get_error_message()]);
    }

    $updated = wp_update_post([
        'ID'           => $target_id,
        'post_title'   => $tr['title'],
        'post_content' => $tr['content'],
        'post_excerpt' => $tr['excerpt'],
    ], true);
    if (is_wp_error($updated)) {
        wp_send_json_error(['message' => $updated->get_error_message()]);
    }

    // Re-translate declared text meta (keeps target-only meta intact).
    snel_translate_post_meta($source_id, $target_id, $source_lang, $target);

    update_post_meta($target_id, SNEL_SRC_HASH_META, snel_source_signature($source_id));

    wp_send_json_success([
        'edit_url' => get_edit_post_link($target_id, 'raw'),
        'post_id'  => $target_id,
    ]);
}

/**
 * Copy post meta that should carry across to a translation, skipping internal
 * and language-system keys.
 */
function snel_copy_translatable_meta(int $from, int $to): void
{
    $skip = [
        TranslationGroup::META_LANG,
        TranslationGroup::META_GROUP,
        '_edit_lock',
        '_edit_last',
        '_wp_old_slug',
        '_wp_old_date',
    ];

    foreach (get_post_meta($from) as $key => $values) {
        if (in_array($key, $skip, true)) {
            continue;
        }
        delete_post_meta($to, $key);
        foreach ($values as $value) {
            add_post_meta($to, $key, maybe_unserialize($value));
        }
    }
}

/**
 * AI-translate declared text meta keys onto a translation, in place.
 *
 * The block walker handles post_content; this is its counterpart for custom
 * fields. Every key in snel_translatable_meta_keys() that holds a non-empty
 * string on the source is translated in one batch and written to $to (which
 * already has the copied, source-language value).
 *
 * Scope: flat text/textarea-style meta (plain ACF text, textarea, wysiwyg).
 * Repeaters and nested groups are NOT handled — translate those by hand. On AI
 * failure the copied source-language values are left untouched (never blank).
 */
function snel_translate_post_meta(int $from, int $to, string $source, string $target): void
{
    $keys = snel_translatable_meta_keys(get_post_type($from));
    if (empty($keys)) {
        return;
    }

    // Collect the non-empty string values to translate, preserving key order.
    $values = [];
    foreach ($keys as $key) {
        $value = get_post_meta($from, $key, true);
        if (is_string($value) && trim($value) !== '') {
            $values[$key] = $value;
        }
    }
    if (empty($values)) {
        return;
    }

    $translated = snel_ai_translate(array_values($values), $source, $target);
    if (is_wp_error($translated)) {
        return; // Leave the copied source-language values in place.
    }

    $i = 0;
    foreach ($values as $key => $original) {
        if (isset($translated[$i])) {
            update_post_meta($to, $key, $translated[$i]);
        }
        $i++;
    }
}

/**
 * Meta keys whose values should be AI-translated when creating a translation.
 * Declare project ACF/custom text fields here, e.g. ['subtitle', 'description'].
 *
 * Filterable so plugins/CPTs can extend the list per post type.
 *
 * @return string[]
 */
function snel_translatable_meta_keys(string $post_type): array
{
    // Add project meta / ACF text fields here: ['subtitle', 'description'].
    $keys = [];

    /**
     * Filter the meta keys that get AI-translated into a new translation.
     *
     * @param string[] $keys
     * @param string   $post_type
     */
    return apply_filters('snel_translatable_meta_keys', $keys, $post_type);
}

// ---------------------------------------------------------------------------
// Block content translation
// ---------------------------------------------------------------------------

/**
 * Translate the text inside a post's block content, returning new block markup.
 *
 * Blocks are monolingual: each post is one language. We translate the text-
 * bearing attributes of known Snel blocks (see snel_block_text_attrs) plus the
 * inner text of core blocks (paragraph, heading, list…), then re-serialize.
 */
function snel_translate_block_content(string $content, string $source, string $target)
{
    if (trim($content) === '') {
        return $content;
    }

    $blocks  = parse_blocks($content);
    $strings = [];
    snel_blocks_collect_strings($blocks, $strings);

    if (empty($strings)) {
        return $content;
    }

    $translated = snel_ai_translate($strings, $source, $target);
    if (is_wp_error($translated)) {
        return $translated;
    }

    $idx = 0;
    snel_blocks_apply_strings($blocks, $translated, $idx);

    return serialize_blocks($blocks);
}

/**
 * Text-bearing attribute keys per Snel block. Core blocks keep their text in
 * inner HTML, so they're handled generically.
 */
function snel_block_text_attrs(string $name): array
{
    // Add custom Snel blocks here: 'snel/your-block' => ['heading', 'content'].
    $map = [];
    return $map[$name] ?? [];
}

/**
 * Pass 1 — collect translatable source strings in deterministic order.
 */
function snel_blocks_collect_strings(array $blocks, array &$out): void
{
    foreach ($blocks as $block) {
        $name = $block['blockName'] ?? '';

        foreach (snel_block_text_attrs($name) as $key) {
            $val = $block['attrs'][$key] ?? '';
            if (is_string($val) && trim($val) !== '') {
                $out[] = $val;
            }
        }

        if (! empty($block['innerBlocks'])) {
            snel_blocks_collect_strings($block['innerBlocks'], $out);
        } else {
            foreach (($block['innerContent'] ?? []) as $chunk) {
                if (is_string($chunk) && trim(wp_strip_all_tags($chunk)) !== '') {
                    $out[] = $chunk;
                }
            }
        }
    }
}

/**
 * Pass 2 — write translations back in the same order they were collected.
 */
function snel_blocks_apply_strings(array &$blocks, array $translations, int &$idx): void
{
    foreach ($blocks as &$block) {
        $name = $block['blockName'] ?? '';

        foreach (snel_block_text_attrs($name) as $key) {
            $val = $block['attrs'][$key] ?? '';
            if (is_string($val) && trim($val) !== '') {
                $block['attrs'][$key] = $translations[$idx] ?? $val;
                $idx++;
            }
        }

        if (! empty($block['innerBlocks'])) {
            snel_blocks_apply_strings($block['innerBlocks'], $translations, $idx);
        } else {
            $has_text = false;
            foreach (($block['innerContent'] ?? []) as $ci => $chunk) {
                if (is_string($chunk) && trim(wp_strip_all_tags($chunk)) !== '') {
                    $block['innerContent'][$ci] = $translations[$idx] ?? $chunk;
                    $idx++;
                    $has_text = true;
                }
            }
            if ($has_text) {
                $block['innerHTML'] = implode('', array_filter($block['innerContent'], 'is_string'));
            }
        }
    }
    unset($block);
}
