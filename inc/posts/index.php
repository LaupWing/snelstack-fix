<?php
/**
 * Posts — helper for the snel/posts carousel block.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_get_recent_posts(array $args = []): array
{
    return get_posts(array_merge([
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
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

// Match main query posts_per_page to the archive block default so /blog/page/N/ doesn't 404.
add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() || ! $q->is_main_query()) {
        return;
    }
    if ($q->is_home() || $q->is_category()) {
        $q->set('posts_per_page', 9);
    }
});
