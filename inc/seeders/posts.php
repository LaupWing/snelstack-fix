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
