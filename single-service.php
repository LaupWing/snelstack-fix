<?php
/**
 * Single service template — full-width Gutenberg blocks, no prose wrapper.
 *
 * @package Snel
 */

get_header();
if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
endif;
get_footer();
