<?php

/**
 * Taxonomy term translation.
 *
 * Model: unlike posts (one post per language), a term is **shared** across all
 * languages. The term keeps its native (default-language) name, slug and
 * description; translations of the *label* and *description* live in term meta.
 * This keeps term relationships and URLs intact — no per-language duplicate
 * terms, no routing changes.
 *
 * Stored in term meta (one pair per non-default language):
 *   _snel_name_{lang}  — the term name in that language (e.g. _snel_name_en)
 *   _snel_desc_{lang}  — the term description in that language
 *
 * The default language always uses the native `name` / `description` columns,
 * so it has no meta. A missing translation falls back to the native value —
 * a term never renders blank.
 *
 * On the frontend the `get_term` filter swaps `name`/`description` into the
 * current language. In wp-admin and the block editor the current language is
 * always the default, so the filter is a no-op there and edit screens keep
 * showing the native term — exactly what you want when editing.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

class TermTranslation
{
    /**
     * Get a term's name in a language (or the current language by default).
     * Falls back to the native name when no translation is stored.
     *
     * @param int|WP_Term $term
     */
    public static function name($term, ?string $lang = null): string
    {
        $term = get_term($term);
        if (! $term instanceof WP_Term) {
            return '';
        }

        $lang = $lang ?: LocaleManager::current();
        if ($lang === LocaleManager::default()) {
            return $term->name;
        }

        $value = get_term_meta($term->term_id, self::nameKey($lang), true);
        return $value !== '' ? $value : $term->name;
    }

    /**
     * Get a term's description in a language (or the current language).
     * Falls back to the native description.
     *
     * @param int|WP_Term $term
     */
    public static function description($term, ?string $lang = null): string
    {
        $term = get_term($term);
        if (! $term instanceof WP_Term) {
            return '';
        }

        $lang = $lang ?: LocaleManager::current();
        if ($lang === LocaleManager::default()) {
            return $term->description;
        }

        $value = get_term_meta($term->term_id, self::descKey($lang), true);
        return $value !== '' ? $value : $term->description;
    }

    /** Meta key for a term name in a given language. */
    public static function nameKey(string $lang): string
    {
        return '_snel_name_' . $lang;
    }

    /** Meta key for a term description in a given language. */
    public static function descKey(string $lang): string
    {
        return '_snel_desc_' . $lang;
    }

    /**
     * Register the frontend display filter. Swaps name/description into the
     * current language. No-op in admin (current language is always default).
     */
    public static function register(): void
    {
        if (is_admin()) {
            return;
        }
        add_filter('get_term', [self::class, 'filterTerm']);
    }

    /**
     * Swap a term's name/description into the current language for display.
     *
     * @param WP_Term $term
     * @return WP_Term
     */
    public static function filterTerm($term)
    {
        if (! $term instanceof WP_Term) {
            return $term;
        }

        $lang = LocaleManager::current();
        if ($lang === LocaleManager::default()) {
            return $term;
        }

        $name = get_term_meta($term->term_id, self::nameKey($lang), true);
        if ($name !== '') {
            $term->name = $name;
        }

        $desc = get_term_meta($term->term_id, self::descKey($lang), true);
        if ($desc !== '') {
            $term->description = $desc;
        }

        return $term;
    }
}
