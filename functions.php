<?php

if (! defined('ABSPATH')) exit;

// ─── Translation fallbacks ───────────────────────────────────────────────────
// The Snel Translations plugin provides these; it loads first (plugins_loaded)
// and wins. These guarded no-op versions only kick in when the plugin is off,
// so the theme degrades to monolingual instead of white-screening.
if (! function_exists('snel__')) {
    function snel__($text) { return $text; }
}
if (! function_exists('snel_url')) {
    function snel_url($url) { return $url; }
}
if (! function_exists('snel_lang_url')) {
    function snel_lang_url($lang) { return home_url('/'); }
}
if (! function_exists('snel_get_default_lang')) {
    function snel_get_default_lang() { return 'nl'; }
}
if (! function_exists('snel_get_lang')) {
    function snel_get_lang() { return 'nl'; }
}
if (! function_exists('snel_get_supported_langs')) {
    function snel_get_supported_langs() { return ['nl']; }
}
if (! function_exists('snel_get_languages_config')) {
    function snel_get_languages_config() {
        return ['nl' => ['label' => 'NL', 'locale' => 'nl_NL', 'default' => true]];
    }
}
if (! function_exists('snel_nav_item')) {
    function snel_nav_item($item) {
        return ['url' => $item->url ?? '#', 'title' => $item->title ?? ''];
    }
}

require get_template_directory() . '/inc/admin/business/index.php';
require get_template_directory() . '/inc/admin/snelstack/index.php';
// Translation system now lives in the Snel Translations plugin (required).
// Declare which Snel block attributes hold translatable text. Blocks whose text
// is stored in attributes (not inner HTML) need this, or they stay untranslated.
add_filter('snel_block_text_attrs', function ($map) {
    $map['snel/statement']       = ['heading', 'paragraph'];
    $map['snel/button']          = ['label'];
    $map['snel/button-gradient'] = ['label'];
    $map['snel/badge-text']      = ['label'];
    $map['snel/posts']           = ['heading'];
    $map['snel/thumbnail']       = ['backLabel'];
    return $map;
});

// Static UI strings baked into block markup (via snel__). Register them so they
// appear in the Theme Strings grid for the translator to fill.
add_filter('snel_theme_string_defaults', function ($groups) {
    $add = function (&$groups, $section, array $keys) {
        foreach ($keys as $k) {
            if (! isset($groups[$section][$k])) $groups[$section][$k] = [];
        }
    };
    $add($groups, 'Blocks', [
        'Lees artikel',
        'Meer cases bekijken',
        'Score',
        'Vorige',
        'Volgende',
        'Paginering',
        'Publiceer blogberichten om dit blok te vullen.',
        'Voeg cases toe via het Cases menu om dit blok te vullen.',
        'Add some Partners (with a logo) under the Partners menu to fill this marquee.',
    ]);
    $add($groups, 'Contactformulier', [
        'Naam', 'E-mailadres', 'Telefoonnummer', '(optioneel)', 'Bericht',
        'Jan de Vries', 'jan@bedrijf.nl', '+31 6 12 34 56 78',
        'Vertel ons over jouw project, idee of vraag...', 'Verstuur bericht',
    ]);
    $add($groups, 'Stack showcase', ['Verken de stack', 'Klik om te verkennen']);
    $add($groups, 'Navigatie', ['Home', 'Terug naar %s']);
    return $groups;
});

// Repeater blocks: array attributes whose items hold translatable text.
add_filter('snel_block_repeater_attrs', function ($map) {
    $map['snel/process']        = ['steps'  => ['title', 'heading', 'body', 'btn_label']];
    $map['snel/stack-showcase'] = ['slides' => ['title', 'text', 'cta']];
    $map['snel/features']       = ['cards'  => ['heading', 'body']];
    return $map;
});
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

