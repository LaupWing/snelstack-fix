<?php
/**
 * Service CPT archive — renders the Gutenberg "AI Diensten" page.
 *
 * @package Snel
 */

get_header();

$page = get_page_by_path('ai-diensten');
if ($page) {
    echo apply_filters('the_content', $page->post_content);
}

get_footer();
