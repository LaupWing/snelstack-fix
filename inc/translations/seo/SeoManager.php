<?php

/**
 * Manages SEO output for multilingual pages.
 *
 * Responsible for:
 * - Outputting hreflang tags (tells Google about language variants)
 * - Outputting a language-aware canonical URL
 * - Outputting a meta description
 *
 * With one post per language, each post already has its own native title and
 * excerpt — there's no per-language meta to look up. hreflang lists only the
 * languages a page is actually translated into.
 *
 * The theme owns all of this directly (no SEO plugin dependency), including
 * the <html lang="..."> attribute.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

class SeoManager
{
    /**
     * Register all WordPress hooks for SEO.
     */
    public static function register(): void
    {
        // hreflang is always the theme's job — an SEO plugin can't know that
        // two separate posts are translations of each other.
        add_action('wp_head', [self::class, 'hreflangTags']);

        // Output the correct <html lang="nl-NL"> for the current language.
        add_filter('language_attributes', [self::class, 'languageAttributes']);

        // Canonical + meta description are owned by Yoast (or any SEO plugin)
        // when present. Only fall back to the theme's own output when no SEO
        // plugin is active, so the boilerplate is never left bare.
        if (! self::seoPluginActive()) {
            remove_action('wp_head', 'rel_canonical');
            add_action('wp_head', [self::class, 'canonicalTag']);
            add_action('wp_head', [self::class, 'metaDescription'], 1);
        }
    }

    /**
     * Whether a dedicated SEO plugin (Yoast / Rank Math / AIOSEO) is handling
     * canonical, meta description, Open Graph, schema and sitemaps.
     */
    public static function seoPluginActive(): bool
    {
        return defined('WPSEO_VERSION')           // Yoast SEO
            || defined('RANK_MATH_VERSION')       // Rank Math
            || defined('AIOSEO_VERSION');         // All in One SEO
    }

    /**
     * Set the <html lang="..."> attribute to the current language's locale.
     * (Previously handled by the Snel SEO plugin; the theme owns it now.)
     */
    public static function languageAttributes($output): string
    {
        $lang   = LocaleManager::current();
        $config = LocaleManager::config();
        $locale = $config[$lang]['locale'] ?? $lang;
        $tag    = strtolower(str_replace('_', '-', $locale));

        $output = preg_replace('/lang="[^"]*"/', 'lang="' . esc_attr($tag) . '"', (string) $output);
        if (strpos($output, 'lang=') === false) {
            $output .= ' lang="' . esc_attr($tag) . '"';
        }
        return $output;
    }

    /**
     * Output hreflang tags in the <head>.
     *
     * For singular content, lists each language the page is translated into
     * (pointing at the sibling's real URL). For other pages, lists every
     * supported language using prefix-swapped URLs.
     */
    public static function hreflangTags(): void
    {
        $config  = LocaleManager::config();
        $default = LocaleManager::default();

        $urls = self::alternateUrls(); // [lang => url]
        if (empty($urls)) {
            return;
        }

        foreach ($urls as $lang => $url) {
            $locale   = $config[$lang]['locale'] ?? $lang;
            $hreflang = strtolower(str_replace('_', '-', $locale));
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($url) . '" />' . "\n";
        }

        // x-default points to the default language version when present.
        if (isset($urls[$default])) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($urls[$default]) . '" />' . "\n";
        }
    }

    /**
     * Build the map of [lang => url] for the current request.
     */
    private static function alternateUrls(): array
    {
        $langs = LocaleManager::supported();

        if (is_singular() || is_page()) {
            $current_id = get_queried_object_id();
            if ($current_id) {
                $siblings = TranslationGroup::siblings(TranslationGroup::groupOf($current_id));
                $urls = [];
                foreach ($siblings as $lang => $post_id) {
                    if (get_post_status($post_id) !== 'publish') {
                        continue;
                    }
                    $urls[$lang] = UrlGenerator::prefixedPermalink($post_id, $lang);
                }
                return $urls;
            }
        }

        // Non-singular: every supported language via prefix swap.
        $urls = [];
        foreach ($langs as $lang) {
            $urls[$lang] = UrlGenerator::langUrl($lang);
        }
        return $urls;
    }

    /**
     * Output a language-aware canonical URL pointing at the current page.
     */
    public static function canonicalTag(): void
    {
        $lang = LocaleManager::current();
        $url  = UrlGenerator::langUrl($lang);

        echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
    }

    /**
     * Output a meta description for the current post.
     *
     * Uses the post's own excerpt, then a trimmed version of its content.
     * (Each post is in a single language, so no per-language lookup needed.)
     */
    public static function metaDescription(): void
    {
        if (! is_singular() && ! is_page()) {
            return;
        }

        $post = get_queried_object();
        if (! $post instanceof WP_Post) {
            return;
        }

        $desc = $post->post_excerpt;
        if (! $desc) {
            $desc = wp_trim_words(wp_strip_all_tags($post->post_content), 25, '...');
        }

        if ($desc) {
            echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
        }
    }
}
