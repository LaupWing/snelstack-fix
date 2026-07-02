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
 * Keep the language/missing columns narrow so the title column gets the space.
 */
function snel_admin_lang_column_widths(): void
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (! $screen || $screen->base !== 'edit') {
        return;
    }
    echo '<style>
        .column-snel_lang { width: 72px; }
        .column-snel_langs { width: 220px; }
    </style>';
}
add_action('admin_head', 'snel_admin_lang_column_widths');

/**
 * Insert a "Language" column just before the Date column.
 */
function snel_admin_lang_column(array $columns): array
{
    $new = [];
    foreach ($columns as $key => $label) {
        if ($key === 'date') {
            $new['snel_lang']  = __('Language', 'snel');
            $new['snel_langs'] = __('Languages', 'snel');
        }
        $new[$key] = $label;
    }
    if (! isset($new['snel_lang'])) {
        $new['snel_lang']  = __('Language', 'snel');
        $new['snel_langs'] = __('Languages', 'snel');
    }
    return $new;
}

/**
 * Render the language badge for a row.
 */
function snel_admin_lang_column_render($column, $post_id): void
{
    if ($column === 'snel_lang') {
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
        return;
    }

    if ($column === 'snel_langs') {
        $config  = snel_get_languages_config();
        $default = snel_get_default_lang();
        $lang    = snel_post_lang($post_id);

        // Translation rows point back to their source (default-language) post.
        if ($lang !== $default) {
            $source_id = (int) snel_get_translation($post_id, $default);
            if ($source_id) {
                printf(
                    '<span style="color:#64748b;font-size:12px;">%s <a href="%s">%s</a></span>',
                    esc_html__('src:', 'snel'),
                    esc_url((string) get_edit_post_link($source_id)),
                    esc_html(get_the_title($source_id))
                );
            } else {
                echo '<span style="color:#cbd5e1;">&mdash;</span>';
            }
            return;
        }

        // Source rows show every language with a check (exists) or cross (missing).
        $siblings = snel_get_translations($post_id);
        foreach (snel_get_supported_langs() as $code) {
            $label = $config[$code]['label'] ?? strtoupper($code);
            $has   = ($code === $default) || ! empty($siblings[$code]);

            $style = $has
                ? 'background:#e7f6ec;color:#15803d;'
                : 'background:#f1f5f9;color:#94a3b8;';
            $icon  = $has ? '&#10003;' : '&#10007;';

            echo '<span style="display:inline-flex;align-items:center;gap:3px;margin:1px 3px 1px 0;padding:2px 7px;border-radius:10px;font-size:11px;font-weight:600;' . $style . '">'
                . $icon . ' ' . esc_html($label)
                . '</span>';
        }
        return;
    }
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
    $default = snel_get_default_lang();
    // Default the filter to the source language, so every list shows the
    // default-language posts first. "All languages" (value "all") opts out.
    $current = isset($_GET['snel_lang_filter'])
        ? sanitize_text_field(wp_unslash($_GET['snel_lang_filter']))
        : $default;

    echo '<select name="snel_lang_filter">';
    echo '<option value="all"' . selected($current, 'all', false) . '>' . esc_html__('All languages', 'snel') . '</option>';
    foreach (snel_get_supported_langs() as $lang) {
        $label = $config[$lang]['label'] ?? strtoupper($lang);
        if ($lang === $default) {
            $label .= ' · ' . __('src', 'snel');
        }
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

    $default = snel_get_default_lang();
    // No param present → default to the source language (matches the dropdown).
    // "all" (or anything not a real language) → don't filter.
    $lang = isset($_GET['snel_lang_filter'])
        ? sanitize_text_field(wp_unslash($_GET['snel_lang_filter']))
        : $default;
    if (! $lang || ! in_array($lang, snel_get_supported_langs(), true)) {
        return;
    }

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
