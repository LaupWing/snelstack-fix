<?php

/**
 * Blog / posts page template.
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
