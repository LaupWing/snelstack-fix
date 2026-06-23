<?php

/**
 * Fallback template — renders block content for any page/post not covered by
 * a more specific template (single.php, page.php, archive.php, etc.).
 *
 * @package Snel
 */

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
endif;

get_footer();
