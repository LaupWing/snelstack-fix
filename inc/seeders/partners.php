<?php
/**
 * Partners seeder.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_seed_partners(bool $wipe = false): int
{
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if ($wipe) {
        $old = get_posts(['post_type' => 'snel_partner', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids']);
        foreach ($old as $id) {
            $thumb_id = get_post_thumbnail_id($id);
            if ($thumb_id) wp_delete_attachment($thumb_id, true);
            wp_delete_post($id, true);
        }
    }

    $logo_dir = get_template_directory() . '/assets/images/partners/';
    $partners = [
        ['name' => 'Morris & Bella',        'url' => 'https://morrisenbella.nl',      'logo' => $logo_dir . 'morrisenbella.png'],
        ['name' => 'Antique Warehouse',      'url' => 'https://antiquewarehouse.nl',   'logo' => $logo_dir . 'antiquewarehouse.png'],
        ['name' => 'DroneStart',             'url' => 'https://dronestart.nl',         'logo' => $logo_dir . 'dronestart.png'],
        ['name' => 'Drone Consultancy',      'url' => 'https://droneconsultancy.nl',   'logo' => $logo_dir . 'droneconsultancy.png'],
        ['name' => 'The Golden Glow',        'url' => 'https://thegoldenglow.nl',      'logo' => $logo_dir . 'thegoldenglow.png'],
    ];

    $count = 0;
    foreach ($partners as $idx => $data) {
        if (! $wipe) {
            $existing = get_posts(['post_type' => 'snel_partner', 'title' => $data['name'], 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids']);
            if ($existing) continue;
        }

        $post_id = wp_insert_post([
            'post_type'   => 'snel_partner',
            'post_title'  => $data['name'],
            'post_status' => 'publish',
            'menu_order'  => $idx,
        ]);
        if (is_wp_error($post_id)) continue;

        update_post_meta($post_id, SNEL_PARTNER_URL_META, $data['url']);

        // Sideload logo from local theme asset.
        if (! empty($data['logo']) && file_exists($data['logo'])) {
            $ext      = pathinfo($data['logo'], PATHINFO_EXTENSION) ?: 'png';
            $tmp_file = tempnam(get_temp_dir(), 'snel') . '.' . $ext;
            copy($data['logo'], $tmp_file);
            $file_array    = ['name' => sanitize_file_name($data['name']) . '.' . $ext, 'tmp_name' => $tmp_file];
            $attachment_id = media_handle_sideload($file_array, $post_id, $data['name']);
            if (! is_wp_error($attachment_id)) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        $count++;
    }

    return $count;
}
