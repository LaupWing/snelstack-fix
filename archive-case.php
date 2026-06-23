<?php
/**
 * Case CPT archive — renders the Gutenberg "Cases" page.
 *
 * @package Snel
 */

get_header();

$page = get_page_by_path('cases');
if ($page) {
    echo apply_filters('the_content', $page->post_content);
}

get_footer();
