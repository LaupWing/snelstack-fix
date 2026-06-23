<?php

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('snelstack-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('snelstack', get_template_directory_uri() . '/build/index.css', ['snelstack-fonts'], '1.0.0');
});
