<?php
/**
 * Case CPT archive — renders the Gutenberg "Cases" page.
 *
 * @package Snel
 */

get_header();

// The Cases page holds the archive's block content.
$page = snel_page('cases');
if ($page) {
    echo apply_filters('the_content', $page->post_content);
}

get_footer();
