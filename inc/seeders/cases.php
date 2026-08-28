<?php
/**
 * Cases — backfill tool. De sample-cases seeder is verwijderd; cases worden
 * handmatig aangemaakt (het case-CPT template zet het skelet klaar).
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

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
