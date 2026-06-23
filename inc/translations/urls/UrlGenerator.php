<?php

/**
 * Builds language-aware URLs.
 *
 * Adds language prefixes for non-default languages (default language has no
 * prefix). For singular content, language URLs point at the *sibling* post's
 * own permalink, so /en/ links resolve to the real English page with its own
 * translated slug.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

class UrlGenerator
{
    /**
     * Cached CPT slug translations config.
     */
    private static ?array $cptSlugs = null;

    /**
     * Load and cache the CPT slug translations config.
     */
    public static function cptSlugsConfig(): array
    {
        if (self::$cptSlugs === null) {
            self::$cptSlugs = require get_template_directory() . '/inc/translations/config/slugs-cpt.php';
        }

        return self::$cptSlugs;
    }

    /**
     * Get the URL for the current page in a different language.
     *
     * For singular content this links to the sibling translation's permalink.
     * If no translation exists, it falls back to that language's homepage.
     * For archives/home/other, it swaps the language prefix on the current URL.
     */
    public static function langUrl(string $target_lang): string
    {
        // Singular content: link to the actual translated post.
        if (is_singular() || is_page()) {
            $current_id = get_queried_object_id();
            if ($current_id) {
                $sibling_id = TranslationGroup::translation($current_id, $target_lang);
                if ($sibling_id) {
                    return self::prefixedPermalink($sibling_id, $target_lang);
                }
                // No translation yet — send visitors to that language's home.
                return self::homeUrl($target_lang);
            }
        }

        // Non-singular (archives, home, search): swap the prefix on the path.
        return self::swapPrefix($target_lang);
    }

    /**
     * Public URL for a post in its own language.
     *
     * The permalink prefix + CPT segment translation are applied centrally by
     * TranslationGroup::filterPermalink, so get_permalink() already returns the
     * correct language-prefixed URL. This is a thin, intention-revealing wrapper.
     */
    public static function prefixedPermalink(int $post_id, string $lang): string
    {
        return get_permalink($post_id);
    }

    /**
     * Build a home-rooted URL for a language: homeUrl('en') → https://site/en/
     */
    private static function homeUrl(string $lang, string $path = ''): string
    {
        $path = trim($path, '/');
        $prefix = ($lang === LocaleManager::default()) ? '' : $lang . '/';
        $full   = $prefix . ($path !== '' ? $path . '/' : '');
        return home_url('/' . $full);
    }

    /**
     * Swap the language prefix on the current request URI (used for archives
     * and other non-singular pages where there's no specific post to link to).
     */
    private static function swapPrefix(string $target_lang): string
    {
        $default     = LocaleManager::default();
        $langs       = LocaleManager::supported();
        $current_url = $_SERVER['REQUEST_URI'] ?? '/';

        $non_default = array_diff($langs, [$default]);
        if (! empty($non_default)) {
            $pattern     = '#^/(' . implode('|', $non_default) . ')(/|$)#';
            $current_url = preg_replace($pattern, '/', $current_url);
        }
        if (empty($current_url)) {
            $current_url = '/';
        }

        if ($target_lang === $default) {
            return home_url($current_url);
        }

        return home_url('/' . $target_lang . $current_url);
    }

    /**
     * Add the current language prefix to any internal URL.
     *
     * If current lang is default ('nl'): returns the URL unchanged.
     * If the URL already has a language prefix: returns unchanged.
     */
    public static function url(string $url): string
    {
        $lang    = LocaleManager::current();
        $default = LocaleManager::default();

        if ($lang === $default) {
            return $url;
        }

        $langs       = LocaleManager::supported();
        $non_default = array_diff($langs, [$default]);
        $parsed      = parse_url($url);
        $path        = $parsed['path'] ?? '/';

        // Avoid double-prefixing.
        $pattern = '#^/(' . implode('|', $non_default) . ')(/|$)#';
        if (preg_match($pattern, $path)) {
            return $url;
        }

        $new_path = '/' . $lang . $path;

        // Rebuild full URL if it had scheme + host.
        if (isset($parsed['scheme'], $parsed['host'])) {
            $host = $parsed['host'];
            if (isset($parsed['port'])) {
                $host .= ':' . $parsed['port'];
            }
            return $parsed['scheme'] . '://' . $host . $new_path;
        }

        return $new_path;
    }
}
