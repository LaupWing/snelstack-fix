<?php

if (! defined('ABSPATH')) exit;

require get_template_directory() . '/inc/admin/business/index.php';
require get_template_directory() . '/inc/blocks/index.php';

add_filter('block_categories_all', function ($cats) {
    return array_merge([['slug' => 'snel', 'title' => 'Snel']], $cats);
});

add_action('init', function () {
    foreach (glob(get_template_directory() . '/build/blocks/sections/*/block.json') as $block) {
        register_block_type($block);
    }
});

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(['primary' => __('Primary Menu', 'snel')]);
});

add_filter('show_admin_bar', '__return_false');

// Remove emoji scripts — unused, saves a request
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
