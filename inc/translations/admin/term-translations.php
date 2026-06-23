<?php

/**
 * Term translation admin UI.
 *
 * Adds a per-language Name + Description field to the edit screen of every
 * public taxonomy (Categories, Tags, and any CPT taxonomy). Values are stored
 * as term meta by TermTranslation. A "Translate with AI" button fills the
 * non-default fields from the native (default-language) name/description in one
 * batch via snel_ai_translate().
 *
 * The native term name/description (the default language) is edited with WP's
 * own fields — we only add the *other* languages.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Hook the translation fields onto every public taxonomy's edit screen.
 */
function snel_term_translations_register(): void
{
    foreach (snel_term_translatable_taxonomies() as $taxonomy) {
        add_action("{$taxonomy}_edit_form_fields", 'snel_term_translation_fields', 10, 2);
        add_action("edited_{$taxonomy}", 'snel_term_translation_save');
        add_action("created_{$taxonomy}", 'snel_term_translation_save');
    }
}
add_action('admin_init', 'snel_term_translations_register');

/**
 * Public taxonomies that should get translation fields. Excludes built-in
 * structural taxonomies that have no user-facing name (post_format, nav_menu…).
 *
 * @return string[]
 */
function snel_term_translatable_taxonomies(): array
{
    $taxonomies = get_taxonomies(['public' => true], 'names');
    unset($taxonomies['post_format']);

    /**
     * Filter the taxonomies whose terms can be translated.
     *
     * @param string[] $taxonomies
     */
    return apply_filters('snel_term_translatable_taxonomies', array_values($taxonomies));
}

/**
 * Render the per-language Name + Description fields on the term edit screen.
 *
 * @param WP_Term $term
 */
function snel_term_translation_fields($term): void
{
    $default = snel_get_default_lang();
    $config  = snel_get_languages_config();

    wp_nonce_field('snel_term_translation', 'snel_term_translation_nonce');
    ?>
    <tr class="form-field">
        <th scope="row"><?php esc_html_e('Translations', 'snel'); ?></th>
        <td>
            <p style="margin-top:0;">
                <button type="button"
                        class="button"
                        id="snel-term-translate"
                        data-term="<?php echo esc_attr($term->term_id); ?>">
                    <?php esc_html_e('Translate with AI', 'snel'); ?>
                </button>
                <span class="spinner" style="float:none;vertical-align:middle;"></span>
                <span id="snel-term-translate-msg" style="margin-left:6px;"></span>
            </p>
        </td>
    </tr>
    <?php
    foreach ($config as $lang => $data) {
        if ($lang === $default) {
            continue;
        }
        $label = $data['label'] ?? strtoupper($lang);
        $name  = get_term_meta($term->term_id, TermTranslation::nameKey($lang), true);
        $desc  = get_term_meta($term->term_id, TermTranslation::descKey($lang), true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="snel_name_<?php echo esc_attr($lang); ?>">
                    <?php echo esc_html(sprintf(__('Name (%s)', 'snel'), $label)); ?>
                </label>
            </th>
            <td>
                <input type="text"
                       name="snel_term_name[<?php echo esc_attr($lang); ?>]"
                       id="snel_name_<?php echo esc_attr($lang); ?>"
                       class="snel-term-name"
                       data-lang="<?php echo esc_attr($lang); ?>"
                       value="<?php echo esc_attr($name); ?>"
                       size="40" />
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="snel_desc_<?php echo esc_attr($lang); ?>">
                    <?php echo esc_html(sprintf(__('Description (%s)', 'snel'), $label)); ?>
                </label>
            </th>
            <td>
                <textarea name="snel_term_desc[<?php echo esc_attr($lang); ?>]"
                          id="snel_desc_<?php echo esc_attr($lang); ?>"
                          class="snel-term-desc"
                          data-lang="<?php echo esc_attr($lang); ?>"
                          rows="3"
                          cols="40"><?php echo esc_textarea($desc); ?></textarea>
            </td>
        </tr>
        <?php
    }
}

/**
 * Persist the per-language Name + Description fields as term meta.
 *
 * @param int $term_id
 */
function snel_term_translation_save(int $term_id): void
{
    if (! isset($_POST['snel_term_translation_nonce'])
        || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['snel_term_translation_nonce'])), 'snel_term_translation')) {
        return;
    }
    if (! current_user_can('edit_term', $term_id)) {
        return;
    }

    $default = snel_get_default_lang();
    $names   = isset($_POST['snel_term_name']) ? (array) wp_unslash($_POST['snel_term_name']) : [];
    $descs   = isset($_POST['snel_term_desc']) ? (array) wp_unslash($_POST['snel_term_desc']) : [];

    foreach (snel_get_supported_langs() as $lang) {
        if ($lang === $default) {
            continue;
        }

        $name = isset($names[$lang]) ? sanitize_text_field($names[$lang]) : '';
        if ($name !== '') {
            update_term_meta($term_id, TermTranslation::nameKey($lang), $name);
        } else {
            delete_term_meta($term_id, TermTranslation::nameKey($lang));
        }

        $desc = isset($descs[$lang]) ? sanitize_textarea_field($descs[$lang]) : '';
        if ($desc !== '') {
            update_term_meta($term_id, TermTranslation::descKey($lang), $desc);
        } else {
            delete_term_meta($term_id, TermTranslation::descKey($lang));
        }
    }
}

/**
 * Enqueue the "Translate with AI" button script on term edit screens.
 *
 * @param string $hook
 */
function snel_term_translation_assets(string $hook): void
{
    if ($hook !== 'term.php') {
        return;
    }
    $taxonomy = isset($_GET['taxonomy']) ? sanitize_key(wp_unslash($_GET['taxonomy'])) : '';
    if (! in_array($taxonomy, snel_term_translatable_taxonomies(), true)) {
        return;
    }

    // Use jQuery (already present on admin term screens) for the AJAX button.
    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery-core', snel_term_translation_inline_js());
}
add_action('admin_enqueue_scripts', 'snel_term_translation_assets');

/**
 * The inline JS powering the AI translate button. Reads the native Name +
 * Description, sends them to the AJAX handler, and fills the per-language
 * fields with the response.
 */
function snel_term_translation_inline_js(): string
{
    $nonce = wp_create_nonce('snel_term_translation_ajax');
    $ajax  = admin_url('admin-ajax.php');

    return <<<JS
jQuery(function ($) {
    var \$btn = $('#snel-term-translate');
    if (!\$btn.length) return;

    \$btn.on('click', function () {
        var \$spinner = \$btn.siblings('.spinner');
        var \$msg = $('#snel-term-translate-msg');
        var name = $('#name').val() || '';
        var desc = $('#description').val() || '';

        if (!name) {
            \$msg.css('color', '#b32d2e').text('Enter a name first.');
            return;
        }

        \$btn.prop('disabled', true);
        \$spinner.addClass('is-active');
        \$msg.css('color', '').text('');

        $.post('{$ajax}', {
            action: 'snel_translate_term',
            nonce: '{$nonce}',
            term_id: \$btn.data('term'),
            name: name,
            description: desc
        }).done(function (res) {
            if (!res || !res.success) {
                \$msg.css('color', '#b32d2e').text((res && res.data && res.data.message) || 'Translation failed.');
                return;
            }
            $.each(res.data.translations, function (lang, t) {
                var \$n = $('.snel-term-name[data-lang="' + lang + '"]');
                var \$d = $('.snel-term-desc[data-lang="' + lang + '"]');
                if (\$n.length) \$n.val(t.name || '');
                if (\$d.length) \$d.val(t.description || '');
            });
            \$msg.css('color', '#007017').text('Translated — review and Update to save.');
        }).fail(function () {
            \$msg.css('color', '#b32d2e').text('Request failed.');
        }).always(function () {
            \$btn.prop('disabled', false);
            \$spinner.removeClass('is-active');
        });
    });
});
JS;
}

/**
 * AJAX: translate a term's native name + description into every non-default
 * language. Returns { translations: { en: {name, description}, ... } }.
 */
function snel_translate_term_ajax(): void
{
    check_ajax_referer('snel_term_translation_ajax', 'nonce');

    $term_id = (int) ($_POST['term_id'] ?? 0);
    if (! $term_id || ! current_user_can('edit_term', $term_id)) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $desc = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';

    if ($name === '') {
        wp_send_json_error(['message' => 'Nothing to translate']);
    }

    $source       = snel_get_default_lang();
    $translations = [];

    foreach (snel_get_supported_langs() as $lang) {
        if ($lang === $source) {
            continue;
        }

        // Batch name + description (when present) into one AI call per language.
        $texts = [$name];
        if ($desc !== '') {
            $texts[] = $desc;
        }

        $result = snel_ai_translate($texts, $source, $lang);
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        $translations[$lang] = [
            'name'        => $result[0] ?? $name,
            'description' => ($desc !== '' && isset($result[1])) ? $result[1] : '',
        ];
    }

    wp_send_json_success(['translations' => $translations]);
}
add_action('wp_ajax_snel_translate_term', 'snel_translate_term_ajax');
