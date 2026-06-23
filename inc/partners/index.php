<?php
defined('ABSPATH') || exit;

if (! defined('SNEL_PARTNER_URL_META')) {
    define('SNEL_PARTNER_URL_META', '_snel_partner_url');
}

function snel_register_partner_cpt(): void
{
    register_post_type('snel_partner', [
        'labels' => [
            'name'          => __('Partners', 'snel'),
            'singular_name' => __('Partner', 'snel'),
            'add_new'       => __('Add Partner', 'snel'),
            'add_new_item'  => __('Add Partner', 'snel'),
            'edit_item'     => __('Edit Partner', 'snel'),
            'new_item'      => __('New Partner', 'snel'),
            'view_item'     => __('View Partner', 'snel'),
            'search_items'  => __('Search Partners', 'snel'),
            'not_found'     => __('No partners yet', 'snel'),
            'all_items'     => __('Partners', 'snel'),
            'menu_name'     => __('Partners', 'snel'),
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-groups',
        'menu_position' => 26,
        'supports'      => ['title', 'thumbnail', 'page-attributes'],
        'has_archive'   => false,
        'rewrite'       => false,
    ]);
}
add_action('init', 'snel_register_partner_cpt');

add_action('add_meta_boxes', function () {
    add_meta_box(
        'snel_partner_url',
        __('Website', 'snel'),
        'snel_partner_url_metabox',
        'snel_partner',
        'side'
    );
});

function snel_partner_url_metabox($post): void
{
    $url = get_post_meta($post->ID, SNEL_PARTNER_URL_META, true);
    wp_nonce_field('snel_partner_url', 'snel_partner_url_nonce');
    echo '<p><input type="url" name="snel_partner_url" class="widefat" placeholder="https://example.com" value="' . esc_attr($url) . '" /></p>';
    echo '<p class="description">' . esc_html__('The logo links here. Set the logo as the Featured image.', 'snel') . '</p>';
}

add_action('save_post_snel_partner', function ($post_id) {
    if (! isset($_POST['snel_partner_url_nonce'])
        || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['snel_partner_url_nonce'])), 'snel_partner_url')) {
        return;
    }
    if (! current_user_can('edit_post', $post_id)) {
        return;
    }
    $url = isset($_POST['snel_partner_url']) ? esc_url_raw(wp_unslash($_POST['snel_partner_url'])) : '';
    update_post_meta($post_id, SNEL_PARTNER_URL_META, $url);
});

function snel_get_partners(): array
{
    return get_posts([
        'post_type'   => 'snel_partner',
        'numberposts' => -1,
        'orderby'     => ['menu_order' => 'ASC', 'title' => 'ASC'],
    ]);
}

function snel_partner_url($post_id): string
{
    return (string) get_post_meta($post_id, SNEL_PARTNER_URL_META, true);
}

add_filter('upload_mimes', function ($mimes) {
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
});
