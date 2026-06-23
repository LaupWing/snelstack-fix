<?php

/**
 * Navigation menu translation (Option C: auto-derive from translation groups).
 *
 * Build ONE menu in the default language. Per request:
 *   - Page/post items resolve to the sibling post in the current language —
 *     link = its permalink, label = its (translated) title. If no translation
 *     exists, fall back to the default-language page (no gap).
 *   - Custom links / taxonomies, and pages with a CUSTOM menu label, fall back
 *     to the static theme-string translation (snel__).
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Resolve a nav menu item to its URL + label for the current language.
 *
 * @param object $item A nav menu item (from wp_get_nav_menu_items / wp_nav_menu).
 * @return array{url:string,title:string}
 */
function snel_nav_item($item): array
{
    $lang = snel_get_lang();

    // Page/post menu item → use its translation sibling.
    if (($item->type ?? '') === 'post_type' && (int) ($item->object_id ?? 0)) {
        $object_id = (int) $item->object_id;
        $sibling   = snel_get_translation($object_id, $lang);
        $target    = $sibling ?: $object_id; // fallback: the default-language page

        // If the menu label matches the page's own title, auto-translate via the
        // sibling. If it's a custom label, translate it as a theme string.
        $is_custom_label = trim((string) $item->title) !== trim((string) get_the_title($object_id));

        return [
            'url'   => get_permalink($target),
            'title' => $is_custom_label ? snel__($item->title) : get_the_title($target),
        ];
    }

    // Custom link / taxonomy / etc. → translate label; prefix internal paths.
    $path = $item->url ? wp_parse_url($item->url, PHP_URL_PATH) : '';
    return [
        'url'   => $path ? snel_url($path) : ($item->url ?? '#'),
        'title' => snel__($item->title),
    ];
}

/**
 * Apply the resolver to every item of menus rendered via wp_nav_menu()
 * (e.g. the footer). header.php calls snel_nav_item() directly.
 */
add_filter('wp_nav_menu_objects', function ($items) {
    foreach ($items as $item) {
        $resolved    = snel_nav_item($item);
        $item->url   = $resolved['url'];
        $item->title = $resolved['title'];
    }
    return $items;
});
