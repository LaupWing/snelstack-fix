<?php
/**
 * Cases seeder.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_case_page_blocks(array $data): string
{
    $visual_map = [
        'SaaS Platform'       => 'software',
        'SaaS Product'        => 'software',
        'White-label Product' => 'software',
        'Custom WordPress'    => 'website',
        'Automatisering'      => 'automation',
    ];
    $visual   = $visual_map[$data['type'] ?? ''] ?? 'software';
    $type     = esc_html($data['type'] ?? '');
    $title    = esc_html($data['title']);
    $result   = esc_html($data['result'] ?? '');
    $live_url = $data['url'] ?? '';

    $cta_inner = '';
    if ($live_url) {
        $cta_inner .= '<!-- wp:snel/button-gradient {"label":"Bekijk live","url":"' . esc_attr($live_url) . '"} /-->' . "\n";
    }
    $cta_inner .= '<!-- wp:snel/button {"label":"Alle cases","url":"/cases"} /-->';

    $b = [];

    // ── Intro ─────────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/intro {"visual":"' . $visual . '"} -->'
         . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text {\"label\":\"" . $type . "\"} /--></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-heading\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-heading\"><!-- wp:snel/heading {\"level\":\"h1\",\"size\":\"xl\",\"weight\":\"extrabold\"} -->"
         . "\n<h1 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-xl snel-hw-extrabold\">" . $title . "</h1>"
         . "\n<!-- /wp:snel/heading --></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-body\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-body\"><!-- wp:snel/paragraph {\"size\":\"md\"} -->"
         . "\n<p class=\"wp-block-snel-paragraph snel-text max-w-4xl snel-text-md\">" . $result . "</p>"
         . "\n<!-- /wp:snel/paragraph --></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n\n<!-- wp:snel/slot {\"max\":2,\"orientation\":\"horizontal\",\"className\":\"snel-slot-cta\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-cta\">" . $cta_inner . "</div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n<!-- /wp:snel/intro -->";

    // ── Thumbnail (featured image + breadcrumb + info cards) ──────────────────
    $flags    = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $services = implode(' · ', (array) ($data['services'] ?? []));
    $thumb_attrs = json_encode([
        'bg'        => 'white',
        'backUrl'   => '/cases/',
        'backLabel' => 'Cases',
        'label1'    => 'Type',
        'value1'    => $data['type'] ?? '',
        'label2'    => 'Klant',
        'value2'    => $data['client'] ?? '',
        'label3'    => 'Technologie',
        'value3'    => $services,
    ], $flags);
    $b[] = '<!-- wp:snel/thumbnail ' . $thumb_attrs . ' /-->';

    // ── Case content ──────────────────────────────────────────────────────────
    $b[] = $data['content'];

    return implode("\n\n", $b);
}

function snel_seed_cases(bool $wipe = false): int
{
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    if ($wipe) {
        $old = get_posts(['post_type' => 'case', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids']);
        foreach ($old as $id) {
            $thumb_id = get_post_thumbnail_id($id);
            if ($thumb_id) wp_delete_attachment($thumb_id, true);
            wp_delete_post($id, true);
        }
    }

    $cases = require get_template_directory() . '/inc/seeders/data/cases.php';

    $count = 0;
    foreach ($cases as $data) {
        $existing = get_posts([
            'post_type'   => 'case',
            'title'       => $data['title'],
            'post_status' => 'any',
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);

        if ($existing) {
            continue;
        }

        $raw_content = snel_case_page_blocks($data);

        $post_id = wp_insert_post([
            'post_type'    => 'case',
            'post_title'   => $data['title'],
            'post_content' => $raw_content,
            'post_status'  => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            continue;
        }

        update_post_meta($post_id, '_case_client',   $data['client']);
        update_post_meta($post_id, '_case_services', $data['services']);
        update_post_meta($post_id, '_case_result',   $data['result']);
        update_post_meta($post_id, '_case_url',      $data['url']);
        update_post_meta($post_id, '_case_type',     $data['type'] ?? '');

        // Sideload featured thumbnail
        if (! empty($data['thumb_url'])) {
            $tmp = download_url($data['thumb_url']);
            if (! is_wp_error($tmp)) {
                $ext        = pathinfo(parse_url($data['thumb_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                $file_array = ['name' => sanitize_title($data['title']) . '.' . $ext, 'tmp_name' => $tmp];
                $att_id     = media_handle_sideload($file_array, $post_id, $data['title']);
                if (! is_wp_error($att_id)) {
                    set_post_thumbnail($post_id, $att_id);
                }
            }
        }

        // Replace snel:image tokens in the body content with sideloaded wp:image blocks
        $processed = snel_process_content_images($raw_content, $post_id);
        if ($processed !== $raw_content) {
            wp_update_post(['ID' => $post_id, 'post_content' => $processed]);
        }

        $count++;
    }

    return $count;
}
