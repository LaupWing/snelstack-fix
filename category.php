<?php
/**
 * Category archive template — renders the posts page Gutenberg content.
 * snel/posts detects is_category() and filters automatically.
 *
 * @package Snel
 */

get_header();

$page_id = get_option('page_for_posts');
if ($page_id) {
    $page = get_post($page_id);
    echo apply_filters('the_content', $page->post_content);
}

get_footer();
