<?php
/**
 * Translations Admin Page — React-powered
 *
 * Centralized page to view and manage all translations:
 * - Theme strings (header, footer, UI text)
 * - Menu items
 * - Page content (Gutenberg block overview)
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

// ─── Register Menu (submenu under "Snel Stack") ─────────────────────────────

function snel_translations_admin_menu()
{
    add_submenu_page(
        'snelstack',
        __('Translations', 'snel'),
        __('Translations', 'snel'),
        'manage_options',
        'snel-translations',
        'snel_translations_page_render'
    );
}
add_action('admin_menu', 'snel_translations_admin_menu', 11);

// ─── REST API Endpoints ─────────────────────────────────────────────────────

add_action('rest_api_init', function () {
    // Get all theme string translations (grouped by section).
    register_rest_route('snel-translations/v1', '/theme-strings', array(
        'methods'             => 'GET',
        'callback'            => function () {
            return rest_ensure_response(Translator::grouped());
        },
        'permission_callback' => function () { return current_user_can('manage_options'); },
    ));

    // Save theme string translations.
    register_rest_route('snel-translations/v1', '/theme-strings', array(
        'methods'             => 'POST',
        'callback'            => function (WP_REST_Request $request) {
            $translations = $request->get_json_params();
            if (! is_array($translations)) {
                return new WP_Error('invalid_data', 'Expected an object of translations.', array('status' => 400));
            }

            foreach ($translations as $dutch_key => $langs) {
                if (! is_array($langs)) continue;
                foreach ($langs as $lang => $text) {
                    snel_save_translation(
                        sanitize_text_field($dutch_key),
                        sanitize_key($lang),
                        sanitize_text_field($text)
                    );
                }
            }

            return rest_ensure_response(array('success' => true));
        },
        'permission_callback' => function () { return current_user_can('manage_options'); },
    ));

    // Get all pages with their translatable blocks.
    register_rest_route('snel-translations/v1', '/pages', array(
        'methods'             => 'GET',
        'callback'            => function () {
            $pages = get_posts(array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC',
            ));

            $langs       = snel_get_supported_langs();
            $default     = snel_get_default_lang();
            $non_default = array_values(array_diff($langs, array($default)));
            $result      = array();

            foreach ($pages as $page) {
                $blocks       = parse_blocks($page->post_content);
                $translatable = snel_translations_extract_blocks($blocks, $non_default, $default);

                // Count completeness.
                $total  = 0;
                $filled = 0;
                foreach ($translatable as $block) {
                    foreach ($block['attributes'] as $attr) {
                        $total += count($non_default);
                        foreach ($non_default as $lang) {
                            if (! empty($attr['values'][$lang])) {
                                $filled++;
                            }
                        }
                    }
                }

                $result[] = array(
                    'id'      => $page->ID,
                    'title'   => $page->post_title,
                    'slug'    => $page->post_name,
                    'editUrl' => get_edit_post_link($page->ID, 'raw'),
                    'blocks'  => $translatable,
                    'total'   => $total,
                    'filled'  => $filled,
                );
            }

            return rest_ensure_response($result);
        },
        'permission_callback' => function () { return current_user_can('manage_options'); },
    ));

});

// ─── Block Defaults Helper ───────────────────────────────────────────────────

/**
 * Merge block.json default attributes into a block's saved attrs.
 * parse_blocks() does not apply defaults — only the renderer does.
 */
function snel_translations_merge_block_defaults($block_name, $attrs)
{
    $slug  = str_replace('snel/', '', $block_name);
    $paths = array(
        get_template_directory() . '/build/blocks/' . $slug . '/block.json',
        get_template_directory() . '/src/blocks/' . $slug . '/block.json',
    );

    foreach ($paths as $path) {
        if (file_exists($path)) {
            $meta = json_decode(file_get_contents($path), true);
            if (! empty($meta['attributes'])) {
                foreach ($meta['attributes'] as $key => $def) {
                    if (! isset($attrs[$key]) && isset($def['default'])) {
                        $attrs[$key] = $def['default'];
                    }
                }
            }
            break;
        }
    }

    return $attrs;
}

// ─── Block Extraction Helper ─────────────────────────────────────────────────

function snel_translations_extract_blocks($blocks, $non_default, $default, &$result = array())
{
    foreach ($blocks as $block) {
        if (! empty($block['blockName']) && strpos($block['blockName'], 'snel/') === 0) {
            $attrs = snel_translations_merge_block_defaults($block['blockName'], $block['attrs'] ?? array());
            $translatable_attrs = array();

            foreach ($attrs as $key => $value) {
                if (is_array($value) && ! isset($value[0])) {
                    $has_lang_keys = false;
                    foreach ($value as $k => $v) {
                        if (in_array($k, array_merge(array($default), $non_default), true) && is_string($v)) {
                            $has_lang_keys = true;
                            break;
                        }
                    }

                    if ($has_lang_keys) {
                        $translatable_attrs[] = array(
                            'key'    => $key,
                            'values' => $value,
                        );
                    }
                }
            }

            if (! empty($translatable_attrs)) {
                $result[] = array(
                    'name'       => $block['blockName'],
                    'label'      => str_replace('snel/', '', $block['blockName']),
                    'attributes' => $translatable_attrs,
                );
            }
        }

        if (! empty($block['innerBlocks'])) {
            snel_translations_extract_blocks($block['innerBlocks'], $non_default, $default, $result);
        }
    }

    return $result;
}

// ─── Menu Items Helper ───────────────────────────────────────────────────────

function snel_translations_get_menu_items()
{
    $locations = get_nav_menu_locations();
    $items     = array();
    $db        = get_option('snel_theme_translations', array());
    $langs     = array_diff(snel_get_supported_langs(), array(snel_get_default_lang()));

    // Get file translations for defaults.
    $file_translations = array();
    $translations_file = get_template_directory() . '/inc/translations/translations.php';
    if (file_exists($translations_file)) {
        $raw = require $translations_file;
        foreach ($raw as $section => $strings) {
            if (is_array($strings) && ! isset($strings['en'])) {
                $file_translations = array_merge($file_translations, $strings);
            }
        }
    }

    foreach ($locations as $location => $menu_id) {
        if (! $menu_id) continue;
        $menu_items = wp_get_nav_menu_items($menu_id);
        if (! $menu_items) continue;

        foreach ($menu_items as $menu_item) {
            $title        = $menu_item->title;
            $translations = array();

            foreach ($langs as $lang) {
                if (! empty($db[$title][$lang])) {
                    $translations[$lang] = $db[$title][$lang];
                } elseif (! empty($file_translations[$title][$lang])) {
                    $translations[$lang] = $file_translations[$title][$lang];
                } else {
                    $translations[$lang] = '';
                }
            }

            $items[] = array(
                'id'           => $menu_item->ID,
                'title'        => $title,
                'translations' => $translations,
                'menu'         => $location,
                'menuName'     => wp_get_nav_menu_object($menu_id)->name ?? $location,
                'parent'       => (int) $menu_item->menu_item_parent,
            );
        }
    }

    return $items;
}

// ─── Enqueue React App ──────────────────────────────────────────────────────

add_action('admin_enqueue_scripts', function ($hook) {
    // Submenu hook is e.g. "snel-stack_page_snel-translations" — match loosely.
    if (strpos($hook, 'snel-translations') === false) return;

    $admin_dir  = get_template_directory() . '/build/admin/translations/';
    $admin_url  = get_template_directory_uri() . '/build/admin/translations/';
    $asset_file = $admin_dir . 'index.asset.php';
    if (! file_exists($asset_file)) return;

    $asset = require $asset_file;

    wp_enqueue_script('snel-translations-admin', $admin_url . 'index.js', $asset['dependencies'], $asset['version'], true);
    wp_enqueue_style('snel-translations-admin', $admin_url . 'index.css', array('wp-components'), $asset['version']);

    $default_lang = snel_get_default_lang();
    $enabled      = snel_get_supported_langs();
    $config       = include get_template_directory() . '/inc/translations/config/languages.php';

    wp_localize_script('snel-translations-admin', 'snelTranslations', array(
        'restUrl'      => rest_url('snel-translations/v1'),
        'nonce'        => wp_create_nonce('wp_rest'),
        'languages'    => array_map(function ($code) use ($default_lang, $enabled, $config) {
            return array(
                'code'    => $code,
                'label'   => $config[$code]['label'] ?? strtoupper($code),
                'default' => $code === $default_lang,
                'enabled' => in_array($code, $enabled, true),
            );
        }, array_keys($config)),
        'defaultLang'      => $default_lang,
        'translationsExist' => TranslationGroup::translationsExist(),
        'themeStrings'     => Translator::grouped(),
        'menuItems'        => snel_translations_get_menu_items(),
        'menuEditUrl'      => admin_url('nav-menus.php'),
    ));

    // Also expose translate AJAX config so TranslationGrid can call the AI translate endpoint.
    wp_localize_script('snel-translations-admin', 'snelTranslate', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('snel_translate_nonce'),
        'langs'   => snel_get_supported_langs(),
        'default' => snel_get_default_lang(),
    ));
});

// ─── Render Page ────────────────────────────────────────────────────────────

function snel_translations_page_render()
{
    if (! current_user_can('manage_options')) {
        return;
    }
    echo '<div class="wrap"><div id="snel-translations-root"></div></div>';
}

// ─── Settings REST: save the default language (used by the Settings tab) ────

add_action('rest_api_init', function () {
    register_rest_route('snel-translations/v1', '/settings', array(
        'methods'             => 'POST',
        'callback'            => function (WP_REST_Request $request) {
            $config      = snel_get_languages_config();
            $all_codes   = array_keys($config);
            $new_default = sanitize_text_field((string) $request->get_param('defaultLang'));

            if (! array_key_exists($new_default, $config)) {
                return new WP_Error('invalid_lang', 'Unknown language.', array('status' => 400));
            }

            // Enabled languages (default is always enabled).
            $enabled_in = $request->get_param('enabledLangs');
            $enabled    = is_array($enabled_in)
                ? array_values(array_intersect($all_codes, array_map('sanitize_text_field', $enabled_in)))
                : $all_codes;
            if (! in_array($new_default, $enabled, true)) {
                $enabled[] = $new_default;
            }

            $old_default = snel_get_default_lang();
            if ($new_default !== $old_default) {
                // Stamp unstamped posts with the OLD default before switching.
                TranslationGroup::backfillMissingLang($old_default);
            }

            update_option('snel_enabled_langs', $enabled);
            update_option('snel_default_lang', $new_default);
            flush_rewrite_rules(); // URL prefixes / available languages changed.

            return rest_ensure_response(array(
                'success'      => true,
                'defaultLang'  => $new_default,
                'enabledLangs' => $enabled,
            ));
        },
        'permission_callback' => function () { return current_user_can('manage_options'); },
    ));
});
