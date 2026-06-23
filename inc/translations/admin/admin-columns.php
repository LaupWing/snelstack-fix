<?php

/**
 * Language column + filter on the post/page list tables.
 *
 * Shows each post's language as a badge and adds a "language" dropdown filter
 * above the list, so translations are easy to spot and filter.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register the column + custom column renderer on all public post types.
 */
function snel_admin_register_lang_columns(): void
{
    foreach (get_post_types(['public' => true]) as $pt) {
        if ($pt === 'attachment') {
            continue;
        }
        // Works for pages too (manage_page_posts_columns / _custom_column).
        add_filter("manage_{$pt}_posts_columns", 'snel_admin_lang_column');
        add_action("manage_{$pt}_posts_custom_column", 'snel_admin_lang_column_render', 10, 2);
    }
}
add_action('admin_init', 'snel_admin_register_lang_columns');

/**
 * Insert a "Language" column just before the Date column.
 */
function snel_admin_lang_column(array $columns): array
{
    $new = [];
    foreach ($columns as $key => $label) {
        if ($key === 'date') {
            $new['snel_lang'] = __('Language', 'snel');
        }
        $new[$key] = $label;
    }
    if (! isset($new['snel_lang'])) {
        $new['snel_lang'] = __('Language', 'snel');
    }
    return $new;
}

/**
 * Render the language badge for a row.
 */
function snel_admin_lang_column_render($column, $post_id): void
{
    if ($column !== 'snel_lang') {
        return;
    }

    $config     = snel_get_languages_config();
    $lang       = snel_post_lang($post_id);
    $label      = $config[$lang]['label'] ?? strtoupper($lang);
    $is_default = $lang === snel_get_default_lang();

    $style = $is_default
        ? 'background:#e7f0ff;color:#1d4ed8;'
        : 'background:#eef2f5;color:#475569;';

    echo '<span style="display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;' . $style . '">'
        . esc_html($label) . ($is_default ? ' · src' : '')
        . '</span>';
}

/**
 * Output the language filter dropdown above the list table.
 */
function snel_admin_lang_filter(): void
{
    global $typenow;
    if (! in_array($typenow, get_post_types(['public' => true]), true)) {
        return;
    }

    $config  = snel_get_languages_config();
    $current = isset($_GET['snel_lang_filter']) ? sanitize_text_field(wp_unslash($_GET['snel_lang_filter'])) : '';

    echo '<select name="snel_lang_filter">';
    echo '<option value="">' . esc_html__('All languages', 'snel') . '</option>';
    foreach (snel_get_supported_langs() as $lang) {
        $label = $config[$lang]['label'] ?? strtoupper($lang);
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($lang),
            selected($current, $lang, false),
            esc_html($label)
        );
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'snel_admin_lang_filter');

/**
 * Apply the language filter to the list query.
 */
function snel_admin_lang_filter_query($query): void
{
    if (! is_admin() || ! $query->is_main_query()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || $screen->base !== 'edit') {
        return;
    }

    $lang = isset($_GET['snel_lang_filter']) ? sanitize_text_field(wp_unslash($_GET['snel_lang_filter'])) : '';
    if (! $lang || ! in_array($lang, snel_get_supported_langs(), true)) {
        return;
    }

    $default = snel_get_default_lang();
    $meta    = $query->get('meta_query');
    if (! is_array($meta)) {
        $meta = [];
    }

    if ($lang === $default) {
        $meta[] = [
            'relation' => 'OR',
            ['key' => TranslationGroup::META_LANG, 'value' => $default],
            ['key' => TranslationGroup::META_LANG, 'compare' => 'NOT EXISTS'],
        ];
    } else {
        $meta[] = ['key' => TranslationGroup::META_LANG, 'value' => $lang];
    }

    $query->set('meta_query', $meta);
}
add_action('pre_get_posts', 'snel_admin_lang_filter_query');
