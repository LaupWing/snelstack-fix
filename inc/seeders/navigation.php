<?php
/**
 * Navigation seeder.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_seed_menu(): bool
{
    $menu_name = 'Hoofdmenu';

    $menu = wp_get_nav_menu_object($menu_name);
    if (! $menu) {
        $menu_id = wp_create_nav_menu($menu_name);
        if (is_wp_error($menu_id)) {
            return false;
        }
        $menu = wp_get_nav_menu_object($menu_id);
    }

    // Assign to primary location.
    $locations            = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menu->term_id;
    set_theme_mod('nav_menu_locations', $locations);

    // Clear existing items so re-seeding is idempotent.
    $existing_items = wp_get_nav_menu_items($menu->term_id);
    if ($existing_items) {
        foreach ($existing_items as $item) {
            wp_delete_post($item->ID, true);
        }
    }

    // AI Diensten (archive) — top-level.
    $services_id = wp_update_nav_menu_item($menu->term_id, 0, [
        'menu-item-title'  => __('AI Diensten', 'snel'),
        'menu-item-type'   => 'post_type_archive',
        'menu-item-object' => 'service',
        'menu-item-status' => 'publish',
    ]);

    // NL services only, excluding Websites (separate top-level item).
    $services = get_posts([
        'post_type'      => 'service',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
        'meta_query'     => [
            'relation' => 'OR',
            ['key' => '_snel_lang', 'compare' => 'NOT EXISTS'],
            ['key' => '_snel_lang', 'value' => 'nl'],
        ],
    ]);

    foreach ($services as $dienst) {
        wp_update_nav_menu_item($menu->term_id, 0, [
            'menu-item-title'     => $dienst->post_title,
            'menu-item-type'      => 'post_type',
            'menu-item-object'    => 'service',
            'menu-item-object-id' => $dienst->ID,
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $services_id,
        ]);
    }

    // Websites — top-level page.
    wp_update_nav_menu_item($menu->term_id, 0, [
        'menu-item-title'  => __('Websites', 'snel'),
        'menu-item-type'   => 'custom',
        'menu-item-url'    => home_url('/websites/'),
        'menu-item-status' => 'publish',
    ]);

    // Blog (posts archive).
    wp_update_nav_menu_item($menu->term_id, 0, [
        'menu-item-title'  => __('Blog', 'snel'),
        'menu-item-type'   => 'post_type_archive',
        'menu-item-object' => 'post',
        'menu-item-status' => 'publish',
    ]);

    // Cases.
    wp_update_nav_menu_item($menu->term_id, 0, [
        'menu-item-title'  => __('Cases', 'snel'),
        'menu-item-type'   => 'custom',
        'menu-item-url'    => home_url('/cases/'),
        'menu-item-status' => 'publish',
    ]);

    return true;
}
