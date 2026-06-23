<?php

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('snelstack', get_template_directory_uri() . '/build/index.css', [], '1.0.0');
});
