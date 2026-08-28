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

    // ── Case slider (breadcrumb + 1 slide; slide falls back to featured image) ─
    $flags    = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $services = implode(' · ', (array) ($data['services'] ?? []));
    $slider_attrs = json_encode([
        'bg'        => 'white',
        'backUrl'   => '/cases/',
        'backLabel' => 'Cases',
    ], $flags);
    $slide_attrs = json_encode([
        'label' => 'Technologie',
        'value' => $services,
    ], $flags);
    $b[] = '<!-- wp:snel/case-slider ' . $slider_attrs . " -->\n"
         . '<!-- wp:snel/case-slide ' . $slide_attrs . " /-->\n"
         . '<!-- /wp:snel/case-slider -->';

    // ── Case content ──────────────────────────────────────────────────────────
    $b[] = $data['content'];

    return implode("\n\n", $b);
}

/**
 * Backfill: vervang het oude snel/thumbnail blok in bestaande cases (alle
 * talen) door snel/case-slider met 1 slide van de featured image. Cases
 * zonder thumbnail-blok (al omgezet) worden overgeslagen.
 */
function snel_backfill_case_sliders(): int
{
    $cases = get_posts([
        'post_type'      => 'case',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ]);

    $flags   = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $updated = 0;

    foreach ($cases as $case) {
        if (! preg_match('/<!-- wp:snel\/thumbnail (\{.*?\}) \/-->/s', $case->post_content, $m)) {
            continue;
        }
        $old = json_decode($m[1], true) ?: [];

        $services = implode(' · ', (array) (get_post_meta($case->ID, '_case_services', true) ?: []));
        $thumb_id = (int) get_post_thumbnail_id($case->ID);

        $slider_attrs = json_encode([
            'bg'        => $old['bg'] ?? 'white',
            'backUrl'   => $old['backUrl'] ?? '/cases/',
            'backLabel' => $old['backLabel'] ?? 'Cases',
        ], $flags);

        $slide = ['label' => $old['label3'] ?? 'Technologie', 'value' => ($old['value3'] ?? '') ?: $services];
        if ($thumb_id) {
            $slide = ['imageId' => $thumb_id] + $slide;
        }
        $slide_attrs = json_encode($slide, $flags);

        $new_block = '<!-- wp:snel/case-slider ' . $slider_attrs . " -->\n"
                   . '<!-- wp:snel/case-slide ' . $slide_attrs . " /-->\n"
                   . '<!-- /wp:snel/case-slider -->';

        wp_update_post([
            'ID'           => $case->ID,
            'post_content' => str_replace($m[0], $new_block, $case->post_content),
        ]);
        $updated++;
    }

    return $updated;
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
