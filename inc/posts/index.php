<?php
/**
 * Posts — helper for the snel/posts carousel block.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_get_recent_posts(array $args = []): array
{
    // Blog posts are independent per language — show the recent posts written
    // in the current language (default also matches posts with no language yet).
    $lang    = snel_get_lang();
    $default = snel_get_default_lang();
    $meta    = ($lang === $default)
        ? [
            'relation' => 'OR',
            ['key' => '_snel_lang', 'value' => $default],
            ['key' => '_snel_lang', 'compare' => 'NOT EXISTS'],
        ]
        : [['key' => '_snel_lang', 'value' => $lang]];

    return get_posts(array_merge([
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => $meta,
    ], $args));
}

// Categories live at /blog/slug/ (matching smit.net structure).
// A top-priority rewrite rule ensures /blog/page/N/ is never stolen by category rules.
add_action('init', function () {
    if (get_option('category_base') !== 'blog') {
        update_option('category_base', 'blog');
    }

    $blog_page_id = get_option('page_for_posts');
    $slug         = $blog_page_id ? get_post_field('post_name', $blog_page_id) : 'blog';

    add_rewrite_rule(
        $slug . '/page/?([0-9]{1,})/?$',
        'index.php?pagename=' . $slug . '&paged=$matches[1]',
        'top'
    );

    $ver = 'snel-blog-paged-v4-' . $slug;
    if (get_option('snel_rewrite_ver') !== $ver) {
        flush_rewrite_rules(false);
        update_option('snel_rewrite_ver', $ver);
    }
});

// New posts open with the same block structure the seeder writes in snel_post_page_blocks().
// Beams and gradient are off here — an editor turns them on per post if the header needs them.
add_action('init', function () {
    $post_type = get_post_type_object('post');
    if (! $post_type) {
        return;
    }

    $post_type->template = [
        ['snel/intro', ['showBeams' => false, 'showGradient' => false], [
            ['snel/slot', ['max' => 1, 'className' => 'snel-slot-eyebrow'], [
                ['snel/badge-text', []],
            ]],
            ['snel/slot', ['max' => 1, 'className' => 'snel-slot-heading'], [
                ['snel/heading', ['level' => 'h1', 'size' => '2xl', 'weight' => 'bold']],
            ]],
        ]],
        ['snel/thumbnail', ['bg' => 'white', 'backUrl' => '/blog/', 'backLabel' => 'Blog']],
        ['snel/content', []],
    ];

    // Locks the page skeleton only. snel/slot and snel/content opt out of the
    // cascade with templateLock:false, so badge, heading and prose stay editable.
    $post_type->template_lock = 'all';
});

// Match main query posts_per_page to the archive block default so /blog/page/N/ doesn't 404.
add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() || ! $q->is_main_query()) {
        return;
    }
    if ($q->is_home() || $q->is_category()) {
        $q->set('posts_per_page', 9);
    }
});

// Syntax highlighting (highlight.js, Tokyo Night tokens) — only on singular
// content that actually contains a code block, so every other page ships 0 bytes.
add_action('wp_enqueue_scripts', function () {
    if (! is_singular()) {
        return;
    }
    $post = get_post();
    if (! $post || (strpos($post->post_content, '<pre') === false && ! has_block('core/code', $post))) {
        return;
    }

    $uri = get_template_directory_uri();
    $dir = get_template_directory();

    wp_enqueue_style('snel-hljs-theme', "$uri/assets/css/tokyo-night-dark.min.css", [], filemtime("$dir/assets/css/tokyo-night-dark.min.css"));
    // Our prose pre panel owns the background; the theme only colors tokens.
    wp_add_inline_style('snel-hljs-theme', '.snel-content .prose pre code.hljs{background:transparent;padding:0}');

    wp_enqueue_script('snel-hljs', "$uri/assets/js/highlight.min.js", [], filemtime("$dir/assets/js/highlight.min.js"), ['in_footer' => true, 'strategy' => 'defer']);
    wp_add_inline_script('snel-hljs', 'document.querySelectorAll(".prose pre code").forEach(function(el){hljs.highlightElement(el)});');
});
