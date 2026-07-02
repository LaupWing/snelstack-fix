<?php

if (! defined('ABSPATH')) exit;

require get_template_directory() . '/inc/admin/business/index.php';
require get_template_directory() . '/inc/admin/snelstack/index.php';
require get_template_directory() . '/inc/translations/language.php';
if (is_admin()) {
    require get_template_directory() . '/inc/translations/admin/admin-translations.php';
}
require get_template_directory() . '/inc/partners/index.php';
require get_template_directory() . '/inc/cases/index.php';
require get_template_directory() . '/inc/services/index.php';
require get_template_directory() . '/inc/posts/index.php';
require get_template_directory() . '/inc/tools/index.php';
require get_template_directory() . '/inc/contact/index.php';
require get_template_directory() . '/src/blocks/helpers/index.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(['primary' => __('Primary Menu', 'snel')]);
    add_theme_support('editor-styles');
    add_editor_style('build/index.css');
    add_editor_style('src/editor.css');
});

add_action('init', function () {
    $blocks_dir = get_template_directory() . '/build/blocks';
    if (! is_dir($blocks_dir)) return;
    foreach (array_merge(
        glob($blocks_dir . '/*/block.json')   ?: [],
        glob($blocks_dir . '/*/*/block.json') ?: []
    ) as $block_json) {
        register_block_type(dirname($block_json));
    }
});

add_filter('block_categories_all', function ($categories) {
    array_unshift($categories, [
        'slug'  => 'snel',
        'title' => __('Snel', 'snel'),
    ]);
    return $categories;
});

add_filter('show_admin_bar', '__return_false');

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_action('wp_head', function () {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('snelstack-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('snelstack', get_template_directory_uri() . '/build/index.css', ['snelstack-fonts'], '1.0.0');
    wp_enqueue_script('snelstack-main', get_template_directory_uri() . '/assets/js/main.js', [], '1.0.0', true);
});

/**
 * Enqueue Snel Stack editor sidebar plugin (translations panel).
 */
function snel_enqueue_editor_plugins()
{
    $asset_file = get_template_directory() . '/build/editor/snelstack/index.asset.php';
    if (! file_exists($asset_file)) {
        return;
    }

    $asset = require $asset_file;

    wp_enqueue_script(
        'snel-editor-snelstack',
        get_template_directory_uri() . '/build/editor/snelstack/index.js',
        $asset['dependencies'],
        $asset['version'],
        true
    );

    wp_localize_script('snel-editor-snelstack', 'snelTranslate', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('snel_translate_nonce'),
        'langs'   => snel_get_supported_langs(),
        'default' => snel_get_default_lang(),
    ));
}
add_action('enqueue_block_editor_assets', 'snel_enqueue_editor_plugins');
