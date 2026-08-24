<?php
/**
 * Posts seeder.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_seed_posts(bool $wipe = false): int
{
    if ($wipe) {
        $old = get_posts(['post_type' => 'post', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids']);
        foreach ($old as $id) {
            wp_delete_post($id, true);
        }
    }

    // Categories
    $categories = [
        'ai-automatisering' => 'AI Automatisering',
        'ai-tools'          => 'AI Tools',
        'wordpress'         => 'WordPress',
        'seo'               => 'SEO',
    ];
    $cat_ids = [];
    foreach ($categories as $slug => $name) {
        $term = get_term_by('slug', $slug, 'category');
        if ($term) {
            $cat_ids[$slug] = (int) $term->term_id;
        } else {
            $result = wp_insert_term($name, 'category', ['slug' => $slug]);
            $cat_ids[$slug] = is_wp_error($result) ? 0 : (int) $result['term_id'];
        }
    }

    $posts = require get_template_directory() . '/inc/seeders/data/posts.php';

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $count = 0;
    foreach ($posts as $data) {
        $existing = get_posts([
            'post_type'   => 'post',
            'name'        => $data['slug'],
            'post_status' => 'any',
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);

        if ($existing) {
            continue;
        }

        $cat_slug  = $data['category'] ?? '';
        $cat_id    = $cat_ids[$cat_slug] ?? 0;
        $cat_label = $categories[$cat_slug] ?? '';
        $content   = snel_post_page_blocks(array_merge($data, ['category_label' => $cat_label]));

        $post_id = wp_insert_post([
            'post_type'     => 'post',
            'post_title'    => $data['title'],
            'post_name'     => $data['slug'],
            'post_content'  => $content,
            'post_excerpt'  => $data['excerpt'],
            'post_status'   => 'publish',
            'post_date'     => $data['date'],
            'post_category' => $cat_id ? [$cat_id] : [],
        ]);

        if (is_wp_error($post_id)) {
            continue;
        }

        // Attach featured image — supports both remote URLs and local file paths.
        if (! empty($data['thumb_url'])) {
            $src = $data['thumb_url'];
            $ext = pathinfo($src, PATHINFO_EXTENSION) ?: 'png';
            if (file_exists($src)) {
                // Local file — copy to temp so media_handle_sideload doesn't delete the original.
                $tmp = tempnam(get_temp_dir(), 'snel') . '.' . $ext;
                copy($src, $tmp);
            } else {
                $tmp = download_url($src);
            }
            if (! is_wp_error($tmp) && file_exists($tmp)) {
                $file_array = [
                    'name'     => $data['slug'] . '.' . $ext,
                    'tmp_name' => $tmp,
                ];
                $att_id = media_handle_sideload($file_array, $post_id, $data['title']);
                if (! is_wp_error($att_id)) {
                    set_post_thumbnail($post_id, $att_id);
                }
            }
        }

        $count++;
    }

    return $count;
}

function snel_post_page_blocks(array $data): string
{
    $flags    = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    $title    = esc_html($data['title']);
    $category = esc_html($data['category_label'] ?? '');

    $b = [];

    // ── Intro: badge (category) + heading only ────────────────────────────────
    $b[] = '<!-- wp:snel/intro -->'
         . "\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-eyebrow\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-eyebrow\"><!-- wp:snel/badge-text {\"label\":\"" . $category . "\"} /--></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-heading\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-heading\"><!-- wp:snel/heading {\"level\":\"h1\",\"size\":\"2xl\",\"weight\":\"bold\"} -->"
         . "\n<h1 class=\"wp-block-snel-heading snel-heading max-w-4xl snel-h-2xl snel-hw-bold\">" . $title . "</h1>"
         . "\n<!-- /wp:snel/heading --></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n\n<!-- wp:snel/slot {\"max\":1,\"className\":\"snel-slot-body\"} -->"
         . "\n<div class=\"wp-block-snel-slot is-layout-flex snel-slot-body\"><!-- wp:snel/date-label /--></div>"
         . "\n<!-- /wp:snel/slot -->"
         . "\n<!-- /wp:snel/intro -->";

    // ── Thumbnail: featured image + breadcrumb back to /blog/ ─────────────────
    $thumb_attrs = json_encode([
        'bg'        => 'white',
        'backUrl'   => '/blog/',
        'backLabel' => 'Blog',
    ], $flags);
    $b[] = '<!-- wp:snel/thumbnail ' . $thumb_attrs . ' /-->';

    // ── Content ───────────────────────────────────────────────────────────────
    $b[] = '<!-- wp:snel/content -->' . "\n" . $data['content'] . "\n" . '<!-- /wp:snel/content -->';

    return implode("\n\n", $b);
}

function snel_process_content_images(string $content, int $post_id): string
{
    return preg_replace_callback(
        '/<!-- snel:image src="([^"]+)" alt="([^"]*)" \/-->/',
        function ($m) use ($post_id) {
            $src = $m[1];
            $alt = html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');

            $tmp = download_url($src);
            if (is_wp_error($tmp)) return '';

            $ext        = pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $file_array = ['name' => 'case-img-' . md5($src) . '.' . $ext, 'tmp_name' => $tmp];
            $att_id     = media_handle_sideload($file_array, $post_id, $alt);

            if (is_wp_error($att_id)) return '';

            $att_url = wp_get_attachment_url($att_id);
            return "<!-- wp:image {\"id\":$att_id,\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n"
                 . "<figure class=\"wp-block-image size-large\"><img src=\"$att_url\" alt=\"$alt\" class=\"wp-image-$att_id\"/></figure>\n"
                 . "<!-- /wp:image -->";
        },
        $content
    );
}

/**
 * Backfill: voeg de snel-slot-body met snel/date-label toe aan bestaande
 * blogposts die hem nog niet hebben, direct onder de heading-slot in de intro.
 *
 * @return int Aantal bijgewerkte posts.
 */
function snel_backfill_date_labels(): int
{
    // Direct op ID's uit de database: get_posts wordt door het
    // vertalingssysteem op taal gefilterd en zou vertaalde posts overslaan.
    global $wpdb;
    $ids = $wpdb->get_col(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_type = 'post'
         AND post_status NOT IN ('trash', 'auto-draft', 'inherit')"
    );

    $updated = 0;

    foreach ($ids as $id) {
        $post = get_post($id);
        if (! $post) {
            continue;
        }
        if (strpos($post->post_content, 'wp:snel/date-label') !== false) {
            continue;
        }

        $blocks  = parse_blocks($post->post_content);
        $changed = false;

        foreach ($blocks as &$block) {
            if ($block['blockName'] !== 'snel/intro') {
                continue;
            }

            $heading_idx = null;
            $body_idx    = null;
            foreach ($block['innerBlocks'] as $i => $inner) {
                if ($inner['blockName'] !== 'snel/slot') {
                    continue;
                }
                $cls = $inner['attrs']['className'] ?? '';
                if ($cls === 'snel-slot-heading' && $heading_idx === null) {
                    $heading_idx = $i;
                }
                if ($cls === 'snel-slot-body' && $body_idx === null) {
                    $body_idx = $i;
                }
            }

            $date_label = [
                'blockName'    => 'snel/date-label',
                'attrs'        => [],
                'innerBlocks'  => [],
                'innerHTML'    => '',
                'innerContent' => [],
            ];

            // Bestaande body-slot: date-label er vooraan in plaatsen.
            if ($body_idx !== null) {
                $slot = &$block['innerBlocks'][$body_idx];
                array_unshift($slot['innerBlocks'], $date_label);

                // Null-placeholder toevoegen vóór de eerste bestaande null,
                // of vlak na de openende div als de slot leeg was.
                $inserted = false;
                foreach ($slot['innerContent'] as $ci => $chunk) {
                    if ($chunk === null) {
                        array_splice($slot['innerContent'], $ci, 0, [null]);
                        $inserted = true;
                        break;
                    }
                }
                if (! $inserted) {
                    // Lege slot: innerContent is één HTML-string, splits die
                    // vóór de sluitende div zodat de label ín de div komt.
                    $chunk = $slot['innerContent'][0] ?? '';
                    $pos   = is_string($chunk) ? strrpos($chunk, '</div>') : false;
                    if ($pos !== false) {
                        $slot['innerContent'] = array_merge(
                            [substr($chunk, 0, $pos), null, substr($chunk, $pos)],
                            array_slice($slot['innerContent'], 1)
                        );
                    } else {
                        array_splice($slot['innerContent'], 1, 0, [null]);
                    }
                }
                unset($slot);

                $changed = true;
                break;
            }

            if ($heading_idx === null) {
                continue;
            }

            $body_slot = [
                'blockName'    => 'snel/slot',
                'attrs'        => ['max' => 1, 'className' => 'snel-slot-body'],
                'innerBlocks'  => [[
                    'blockName'    => 'snel/date-label',
                    'attrs'        => [],
                    'innerBlocks'  => [],
                    'innerHTML'    => '',
                    'innerContent' => [],
                ]],
                'innerHTML'    => "\n" . '<div class="wp-block-snel-slot is-layout-flex snel-slot-body"></div>' . "\n",
                'innerContent' => ["\n" . '<div class="wp-block-snel-slot is-layout-flex snel-slot-body">', null, '</div>' . "\n"],
            ];

            array_splice($block['innerBlocks'], $heading_idx + 1, 0, [$body_slot]);

            // In innerContent staat per innerBlock een null-placeholder; voeg
            // een nieuwe null in direct na de null van de heading-slot.
            $seen = -1;
            foreach ($block['innerContent'] as $ci => $chunk) {
                if ($chunk === null) {
                    $seen++;
                    if ($seen === $heading_idx) {
                        array_splice($block['innerContent'], $ci + 1, 0, ["\n\n", null]);
                        break;
                    }
                }
            }

            $changed = true;
            break;
        }
        unset($block);

        if ($changed) {
            wp_update_post([
                'ID'           => $post->ID,
                'post_content' => wp_slash(serialize_blocks($blocks)),
            ]);
            $updated++;
        }
    }

    return $updated;
}
