<?php
/**
 * Multilingual System — AI Translation
 *
 * AJAX endpoint that translates text via OpenAI API.
 * Requires SNEL_OPENAI_API_KEY defined in wp-config.php.
 *
 * Portable: copy this file to any theme, rename the snel_ prefix.
 */

defined('ABSPATH') || exit;

/**
 * Enqueue translation nonce + ajax URL for the block editor.
 */
function snel_translate_editor_assets() {
    wp_localize_script('wp-blocks', 'snelTranslate', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('snel_translate_nonce'),
        'langs'   => snel_get_supported_langs(),
        'default' => snel_get_default_lang(),
    ]);

    // Scroll-to-block: if ?awScrollTo=snel/block-name is in the URL,
    // select and scroll to the first instance of that block in the editor.
    $scroll_to = sanitize_text_field($_GET['awScrollTo'] ?? '');
    if ($scroll_to) {
        wp_add_inline_script('wp-blocks', '
            (function() {
                var target = ' . wp_json_encode($scroll_to) . ';
                var found = false;
                wp.domReady(function() {
                    var unsubscribe = wp.data.subscribe(function() {
                        if (found) return;
                        var blocks = wp.data.select("core/block-editor").getBlocks();
                        if (!blocks || blocks.length === 0) return;
                        for (var i = 0; i < blocks.length; i++) {
                            if (blocks[i].name === target) {
                                found = true;
                                unsubscribe();
                                wp.data.dispatch("core/block-editor").selectBlock(blocks[i].clientId);
                                setTimeout(function() {
                                    var el = document.querySelector("[data-block=\"" + blocks[i].clientId + "\"]");
                                    if (el) el.scrollIntoView({ behavior: "smooth", block: "center" });
                                }, 500);
                                return;
                            }
                        }
                    });
                });
            })();
        ');
    }
}
add_action('enqueue_block_editor_assets', 'snel_translate_editor_assets');

/**
 * AJAX handler: translate an array of strings.
 *
 * POST params:
 *   texts[]  — array of source strings
 *   source   — source language code (e.g. 'nl')
 *   target   — target language code (e.g. 'en')
 *   nonce    — security nonce
 */
function snel_translate_ajax() {
    check_ajax_referer('snel_translate_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $texts  = $_POST['texts'] ?? [];
    $target = sanitize_text_field($_POST['target'] ?? 'en');
    $source = sanitize_text_field($_POST['source'] ?? 'nl');

    if (empty($texts) || !is_array($texts)) {
        wp_send_json_error('No texts provided');
    }

    // Sanitize input texts (wp_kses_post preserves safe HTML like <strong>, <em>, <a>)
    $texts = array_map('wp_kses_post', $texts);
    $texts = array_values(array_filter($texts, function($t) { return trim($t) !== ''; }));

    if (empty($texts)) {
        wp_send_json_error('No non-empty texts provided');
    }

    $translations = snel_ai_translate($texts, $source, $target);

    if (is_wp_error($translations)) {
        wp_send_json_error($translations->get_error_message());
    }

    wp_send_json_success(['translations' => $translations]);
}
add_action('wp_ajax_snel_translate', 'snel_translate_ajax');

/**
 * Translate an array of strings via the native AI Client (WP 7.0+), which
 * routes to whichever provider the site owner configured under
 * Settings → Connectors (e.g. OpenAI). Reusable across the block editor's
 * translate button and the "Create translation" page-duplication flow.
 *
 * @param array  $texts  Source strings (HTML allowed; tags are preserved).
 * @param string $source Source language code (e.g. 'nl').
 * @param string $target Target language code (e.g. 'en').
 * @return array|WP_Error Aligned array of translations, or WP_Error on failure.
 */
function snel_ai_translate(array $texts, string $source, string $target)
{
    $texts = array_values($texts);
    if (empty($texts)) {
        return [];
    }

    if (! function_exists('wp_ai_client_prompt')) {
        return new WP_Error('snel_ai_unavailable', 'AI Client unavailable. Requires WordPress 7.0+ with a provider configured under Settings → Connectors.');
    }

    $lang_names = [
        'nl' => 'Dutch',
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
        'es' => 'Spanish',
        'it' => 'Italian',
    ];
    $source_name = $lang_names[$source] ?? $source;
    $target_name = $lang_names[$target] ?? $target;

    // Build a numbered list so we can map translations back by position.
    $numbered = [];
    foreach ($texts as $i => $text) {
        $numbered[] = ($i + 1) . '. ' . $text;
    }

    $prompt = "You are a professional translator. Translate accurately and naturally, preserving HTML tags.\n\n"
            . "Translate the following texts from {$source_name} to {$target_name}. "
            . "Return ONLY the translations, numbered the same way (1. 2. 3. etc). "
            . "Keep HTML tags intact. Keep the same tone, style, and formatting.\n\n"
            . implode("\n", $numbered);

    // Note: don't set temperature — some newer OpenAI models (gpt-5, o-series)
    // reject the 'temperature' parameter entirely.
    $builder = wp_ai_client_prompt($prompt);

    if (! $builder->is_supported_for_text_generation()) {
        return new WP_Error('snel_ai_no_provider', 'No AI provider configured. Add one under Settings → Connectors.');
    }

    $output = $builder->generate_text();
    if (is_wp_error($output)) {
        return new WP_Error('snel_ai_failed', 'AI request failed: ' . $output->get_error_message());
    }

    // Parse numbered translations back into an array.
    $translations = [];
    foreach (explode("\n", trim((string) $output)) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $cleaned = preg_replace('/^\d+\.\s*/', '', $line);
        if ($cleaned !== '') {
            $translations[] = $cleaned;
        }
    }

    if (count($translations) !== count($texts)) {
        return new WP_Error('snel_ai_mismatch', 'Translation count mismatch. Expected ' . count($texts) . ', got ' . count($translations));
    }

    return $translations;
}
