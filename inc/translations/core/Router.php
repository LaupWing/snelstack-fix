<?php

/**
 * Handles language-aware URL routing in WordPress.
 *
 * Registers rewrite rules for language prefixes (/en/, /de/) that set a
 * `lang` query var, then resolves each request to the correct post — the
 * one written in that language.
 *
 * Model: one post per language, linked by a translation group
 * (see TranslationGroup). Each post keeps its own native slug, so the
 * English "About us" page lives at /en/about-us/ with the real WP slug
 * "about-us" — no slug-translation meta needed.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

class Router
{
    /**
     * Register all WordPress hooks for routing.
     */
    public static function register(): void
    {
        add_filter('query_vars', [self::class, 'registerQueryVars']);
        add_action('init', [self::class, 'registerRewriteRules']);
        add_action('after_switch_theme', 'flush_rewrite_rules');
        add_filter('request', [self::class, 'interceptLanguageUrl'], 1);
        add_filter('request', [self::class, 'fixFrontPage']);
        add_filter('request', [self::class, 'resolveLanguagePost'], 20);
        add_filter('redirect_canonical', [self::class, 'preventCanonicalRedirect'], 10, 2);

        // Force flush once after deploy if rules are stale.
        $rules_version = 'snel_rewrite_v6';
        if (get_option($rules_version) !== '1') {
            add_action('init', function () use ($rules_version) {
                flush_rewrite_rules();
                update_option($rules_version, '1');
            }, 99);
        }
    }

    /**
     * Register 'lang' as a query variable so WordPress recognizes it.
     */
    public static function registerQueryVars(array $vars): array
    {
        $vars[] = 'lang';
        return $vars;
    }

    /**
     * Register URL rewrite rules for each non-default language.
     *
     *   /en/                    → homepage with lang=en
     *   /en/page/2/             → blog pagination with lang=en
     *   /en/cpt-slug/           → CPT archive with lang=en (from config)
     *   /en/cpt-slug/some-post/ → CPT single with lang=en (from config)
     *   /en/any-slug/           → page/post with lang=en
     */
    public static function registerRewriteRules(): void
    {
        $default   = LocaleManager::default();
        $langs     = LocaleManager::supported();
        $cpt_slugs = UrlGenerator::cptSlugsConfig();

        foreach ($langs as $lang) {
            if ($lang === $default) {
                continue;
            }

            // /en/ → homepage
            add_rewrite_rule("^{$lang}/?$", 'index.php?lang=' . $lang, 'top');

            // /en/page/2/ → blog pagination
            add_rewrite_rule(
                "^{$lang}/page/([0-9]+)/?$",
                'index.php?lang=' . $lang . '&paged=$matches[1]',
                'top'
            );

            // CPT rules from config (e.g., /en/services/, /en/services/my-post/)
            foreach ($cpt_slugs as $dutch_slug => $translations) {
                if (! empty($translations[$lang])) {
                    $translated_slug = $translations[$lang];

                    add_rewrite_rule(
                        "^{$lang}/{$translated_slug}/?$",
                        'index.php?lang=' . $lang . '&post_type=' . $dutch_slug,
                        'top'
                    );
                    add_rewrite_rule(
                        "^{$lang}/{$translated_slug}/([^/]+)/?$",
                        'index.php?lang=' . $lang . '&post_type=' . $dutch_slug . '&name=$matches[1]',
                        'top'
                    );
                    add_rewrite_rule(
                        "^{$lang}/{$translated_slug}/page/([0-9]+)/?$",
                        'index.php?lang=' . $lang . '&post_type=' . $dutch_slug . '&paged=$matches[1]',
                        'top'
                    );
                }
            }

            // Catch-all for pages and posts.
            add_rewrite_rule(
                "^{$lang}/(.+?)/?$",
                'index.php?lang=' . $lang . '&pagename=$matches[1]',
                'top'
            );
        }
    }

    /**
     * Intercept language URLs that WordPress mismatched against another rule
     * (e.g. the attachment rule). Fires early and pins the query vars when it
     * detects a language prefix in the request URI.
     */
    public static function interceptLanguageUrl(array $query_vars): array
    {
        $default     = LocaleManager::default();
        $langs       = LocaleManager::supported();
        $non_default = array_diff($langs, [$default]);

        if (empty($non_default)) {
            return $query_vars;
        }

        $request = trim($_SERVER['REQUEST_URI'] ?? '', '/');
        $request = strtok($request, '?');

        $pattern = '#^(' . implode('|', $non_default) . ')(/(.*))?$#';
        if (! preg_match($pattern, $request, $matches)) {
            return $query_vars;
        }

        $lang = $matches[1];
        $path = isset($matches[3]) ? trim($matches[3], '/') : '';

        if (! empty($query_vars['lang']) && $query_vars['lang'] === $lang) {
            return $query_vars;
        }

        $new_vars = ['lang' => $lang];

        if (empty($path)) {
            return $new_vars;
        }

        if (preg_match('#^page/(\d+)$#', $path, $page_match)) {
            $new_vars['paged'] = (int) $page_match[1];
            return $new_vars;
        }

        $new_vars['pagename'] = $path;
        return $new_vars;
    }

    /**
     * Fix front page loading for non-default languages.
     *
     * The static front page may itself have a translation. If so, /en/ should
     * load the English front page (the sibling), not the Dutch one.
     */
    public static function fixFrontPage(array $query_vars): array
    {
        $lang = $query_vars['lang'] ?? '';

        if (
            $lang &&
            $lang !== LocaleManager::default() &&
            empty($query_vars['pagename']) &&
            empty($query_vars['page_id']) &&
            empty($query_vars['p']) &&
            empty($query_vars['name']) &&
            empty($query_vars['post_type']) &&
            empty($query_vars['s'])
        ) {
            $front_page_id = (int) get_option('page_on_front');
            if ($front_page_id) {
                $sibling = TranslationGroup::translation($front_page_id, $lang);
                $query_vars['page_id'] = $sibling ?: $front_page_id;
            }
        }

        return $query_vars;
    }

    /**
     * Resolve a request to the post written in the current language.
     *
     * WordPress resolves a slug to *a* post, but with one post per language
     * (and slugs that may repeat across languages) it can land on the wrong
     * one. This filter finds the candidate post, and if its language doesn't
     * match the requested language, swaps in the sibling translation by
     * pinning a concrete post id.
     */
    public static function resolveLanguagePost(array $query_vars): array
    {
        // Skip non-content queries (search, feeds, admin, REST).
        if (! empty($query_vars['s']) || is_admin()) {
            return $query_vars;
        }

        $lang = $query_vars['lang'] ?? LocaleManager::default();

        // Locate the candidate post WordPress would otherwise render.
        $post = null;
        if (! empty($query_vars['pagename'])) {
            $post = get_page_by_path($query_vars['pagename']);
            // Catch-all may have caught a blog post, not a page.
            if (! $post) {
                $post = get_page_by_path($query_vars['pagename'], OBJECT, 'post');
            }
        } elseif (! empty($query_vars['name'])) {
            $type = $query_vars['post_type'] ?? 'post';
            $post = get_page_by_path($query_vars['name'], OBJECT, $type);
        } elseif (! empty($query_vars['page_id'])) {
            $post = get_post((int) $query_vars['page_id']);
        } elseif (! empty($query_vars['p'])) {
            $post = get_post((int) $query_vars['p']);
        }

        if (! $post instanceof WP_Post) {
            return $query_vars;
        }

        // Already the right language — pin the id so a duplicate slug in
        // another language can't shadow it, then we're done.
        $target = $post;
        if (TranslationGroup::langOf($post->ID) !== $lang) {
            $sibling_id = TranslationGroup::translation($post->ID, $lang);
            if (! $sibling_id) {
                return $query_vars; // No translation; let WP render what it found.
            }
            $target = get_post($sibling_id);
            if (! $target instanceof WP_Post) {
                return $query_vars;
            }
        }

        return self::pinPost($query_vars, $target);
    }

    /**
     * Replace slug-based query vars with a concrete post id so WordPress
     * loads exactly the post we resolved.
     */
    private static function pinPost(array $query_vars, WP_Post $post): array
    {
        unset(
            $query_vars['pagename'],
            $query_vars['name'],
            $query_vars['p'],
            $query_vars['page_id'],
            $query_vars['attachment']
        );

        if ($post->post_type === 'page') {
            $query_vars['page_id'] = $post->ID;
        } else {
            $query_vars['p']         = $post->ID;
            $query_vars['post_type'] = $post->post_type;
        }

        return $query_vars;
    }

    /**
     * Prevent WordPress from redirecting translated URLs to the canonical
     * (default-language) URL.
     */
    public static function preventCanonicalRedirect($redirect_url, $requested_url)
    {
        $lang = get_query_var('lang', '');

        if ($lang && $lang !== LocaleManager::default()) {
            return false;
        }

        return $redirect_url;
    }
}
