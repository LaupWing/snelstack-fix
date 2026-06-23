<?php

/**
 * Translation groups — the link between a post and its translations.
 *
 * Model: one WordPress post per language. Posts that are translations of
 * each other share a "translation group" id (the same idea as WPML's `trid`
 * or Polylang's translation taxonomy term).
 *
 * Stored in post meta:
 *   _snel_lang   — the language code this post is written in (e.g. 'en')
 *   _snel_group  — the translation group id (shared across all siblings).
 *                  By convention the group id is the post id of the first
 *                  post created in the group (the "root", usually the NL post).
 *
 * A post with no `_snel_lang` is treated as the default language.
 * A post with no `_snel_group` is treated as a group of one (itself).
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

class TranslationGroup
{
    const META_LANG  = '_snel_lang';
    const META_GROUP = '_snel_group';

    /**
     * Per-request cache of group id => [lang => post_id].
     *
     * @var array<int, array<string, int>>
     */
    private static array $cache = [];

    /**
     * The language a post is written in. Falls back to the default language.
     */
    public static function langOf(int $post_id): string
    {
        $lang = get_post_meta($post_id, self::META_LANG, true);
        if ($lang && in_array($lang, LocaleManager::supported(), true)) {
            return $lang;
        }
        return LocaleManager::default();
    }

    /**
     * The translation group id for a post. Falls back to the post's own id
     * (a post is always at minimum a group of one — itself).
     */
    public static function groupOf(int $post_id): int
    {
        $group = (int) get_post_meta($post_id, self::META_GROUP, true);
        return $group > 0 ? $group : $post_id;
    }

    /**
     * All posts in a group, keyed by language code: ['nl' => 12, 'en' => 45].
     *
     * Includes drafts and pending posts so the editor UI can link to
     * translations that aren't published yet. Cached per request.
     */
    public static function siblings(int $group_id): array
    {
        if (isset(self::$cache[$group_id])) {
            return self::$cache[$group_id];
        }

        $query = new WP_Query([
            'post_type'              => 'any',
            'post_status'            => ['publish', 'draft', 'pending', 'private', 'future'],
            'posts_per_page'         => -1,
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_term_cache' => false,
            'meta_query'             => [
                [
                    'key'   => self::META_GROUP,
                    'value' => $group_id,
                ],
            ],
        ]);

        $map = [];
        foreach ($query->posts as $post) {
            $map[self::langOf($post->ID)] = $post->ID;
        }

        // The root post may predate having a group meta; make sure it's included.
        if (! in_array($group_id, $map, true) && get_post($group_id)) {
            $map[self::langOf($group_id)] = $group_id;
        }

        self::$cache[$group_id] = $map;
        return $map;
    }

    /**
     * Find the translation of a post in a given language. Returns the post id
     * or 0 if no translation exists. Returns the post itself if it already is
     * in the requested language.
     */
    public static function translation(int $post_id, string $lang): int
    {
        if (self::langOf($post_id) === $lang) {
            return $post_id;
        }
        $siblings = self::siblings(self::groupOf($post_id));
        return $siblings[$lang] ?? 0;
    }

    /**
     * Attach a post to a translation group as a given language.
     * Pass $group_id = 0 to start a new group rooted at this post.
     */
    public static function link(int $post_id, int $group_id, string $lang): void
    {
        if ($group_id <= 0) {
            $group_id = $post_id;
        }
        update_post_meta($post_id, self::META_GROUP, $group_id);
        update_post_meta($post_id, self::META_LANG, $lang);
        unset(self::$cache[$group_id]);
    }

    /**
     * Ensure every saved post belongs to a group and has a language.
     * Posts created normally (without using the "Create translation" flow)
     * become a group of one in the default language.
     */
    public static function ensureDefaults(int $post_id, WP_Post $post): void
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        if (in_array($post->post_status, ['auto-draft', 'trash'], true)) {
            return;
        }
        // Only manage public, content post types.
        $public = get_post_types(['public' => true]);
        if (! in_array($post->post_type, $public, true)) {
            return;
        }

        if (! get_post_meta($post_id, self::META_LANG, true)) {
            update_post_meta($post_id, self::META_LANG, LocaleManager::default());
        }
        if (! get_post_meta($post_id, self::META_GROUP, true)) {
            update_post_meta($post_id, self::META_GROUP, $post_id);
        }
    }

    /**
     * Allow two posts in different languages to share the same slug.
     *
     * WordPress forces globally-unique slugs per post type, so an English
     * "contact" page would be saved as "contact-2" if a Dutch "contact"
     * already exists. We only want uniqueness *within the same language* —
     * cross-language slug collisions are fine because the router
     * disambiguates by language. The Router resolves the right post per URL.
     */
    public static function uniqueSlug($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug)
    {
        // No collision happened — WP kept the requested slug.
        if ($slug === $original_slug) {
            return $slug;
        }

        global $wpdb;
        $rows = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts}
             WHERE post_name = %s AND post_type = %s AND post_parent = %d
             AND ID <> %d AND post_status NOT IN ('trash', 'auto-draft')",
            $original_slug,
            $post_type,
            (int) $post_parent,
            (int) $post_ID
        ));

        if (empty($rows)) {
            return $slug;
        }

        $my_lang = self::langOf((int) $post_ID);
        foreach ($rows as $other_id) {
            // A genuine same-language collision: keep WP's uniquified slug.
            if (self::langOf((int) $other_id) === $my_lang) {
                return $slug;
            }
        }

        // Every collision is with a different language — keep the clean slug.
        return $original_slug;
    }

    /**
     * Inject the language prefix into a post's permalink.
     *
     * WordPress's get_permalink() has no idea about our /en/ prefixes, so the
     * English post would link to /about-us/ instead of /en/about-us/. This
     * filter rewrites the URL for non-default-language posts — fixing menus,
     * internal links, and any SEO plugin's canonical/OG url for free.
     *
     * Also translates a leading CPT archive segment (e.g. diensten → services).
     *
     * @param string      $url  The permalink.
     * @param int|WP_Post $post The post (id for page_link, object for post_link).
     */
    public static function filterPermalink($url, $post)
    {
        $post = get_post($post);
        if (! $post instanceof WP_Post) {
            return $url;
        }

        $default = LocaleManager::default();
        $lang    = self::langOf($post->ID);
        if ($lang === $default) {
            return $url;
        }

        $parts = wp_parse_url($url);
        if (empty($parts['path'])) {
            return $url;
        }

        // Path relative to the site root (handles subdirectory installs).
        $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
        $home_path = rtrim($home_path, '/');
        $rel = $parts['path'];
        if ($home_path !== '' && strpos($rel, $home_path) === 0) {
            $rel = substr($rel, strlen($home_path));
        }
        $rel  = trim($rel, '/');
        $segs = $rel === '' ? [] : explode('/', $rel);

        // Already prefixed — don't double up.
        if (! empty($segs) && in_array($segs[0], LocaleManager::supported(), true)) {
            return $url;
        }

        // Translate a leading CPT archive segment.
        if (! empty($segs)) {
            $cpt = UrlGenerator::cptSlugsConfig();
            if (! empty($cpt[$segs[0]][$lang])) {
                $segs[0] = $cpt[$segs[0]][$lang];
            }
        }

        $rel      = implode('/', $segs);
        $new_path = '/' . $lang . ($rel !== '' ? '/' . $rel : '') . '/';

        $rebuilt = home_url($new_path);
        if (! empty($parts['query'])) {
            $rebuilt .= '?' . $parts['query'];
        }
        if (! empty($parts['fragment'])) {
            $rebuilt .= '#' . $parts['fragment'];
        }

        return $rebuilt;
    }

    /**
     * Constrain content listings (blog, archives, search) to the current
     * language so an English archive shows English posts only.
     *
     * Default-language posts may not have a `_snel_lang` meta yet, so for the
     * default language we also match posts where the meta is absent.
     */
    public static function filterArchives($query): void
    {
        if (is_admin() || ! $query->is_main_query()) {
            return;
        }
        if ($query->is_singular() || $query->is_404()) {
            return;
        }
        if (! (
            $query->is_home() || $query->is_archive() || $query->is_search()
            || $query->is_post_type_archive() || $query->is_tax()
            || $query->is_category() || $query->is_tag()
        )) {
            return;
        }

        $lang    = $query->get('lang') ?: LocaleManager::current();
        $default = LocaleManager::default();

        $meta = $query->get('meta_query');
        if (! is_array($meta)) {
            $meta = [];
        }

        if ($lang === $default) {
            $meta[] = [
                'relation' => 'OR',
                ['key' => self::META_LANG, 'value' => $default],
                ['key' => self::META_LANG, 'compare' => 'NOT EXISTS'],
            ];
        } else {
            $meta[] = ['key' => self::META_LANG, 'value' => $lang];
        }

        $query->set('meta_query', $meta);
    }

    /**
     * Stamp every public post that has no language yet with a given language.
     * Run before swapping the default language so existing (unstamped) posts
     * keep their real language instead of inheriting the new default.
     *
     * @return int Number of posts stamped.
     */
    public static function backfillMissingLang(string $lang): int
    {
        global $wpdb;

        $types = array_values(get_post_types(['public' => true]));
        if (empty($types)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($types), '%s'));
        $args = array_merge($types, [self::META_LANG]);

        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT p.ID FROM {$wpdb->posts} p
             WHERE p.post_type IN ($placeholders)
             AND p.post_status NOT IN ('auto-draft', 'trash')
             AND NOT EXISTS (
                 SELECT 1 FROM {$wpdb->postmeta} m
                 WHERE m.post_id = p.ID AND m.meta_key = %s
             )",
            $args
        ));

        foreach ($ids as $id) {
            update_post_meta((int) $id, self::META_LANG, $lang);
        }

        return count($ids);
    }

    /**
     * Whether any post exists in a non-default language (i.e. translations
     * have been created). Used to decide whether to warn before swapping default.
     */
    public static function translationsExist(): bool
    {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value <> %s",
            self::META_LANG,
            LocaleManager::default()
        ));

        return (int) $count > 0;
    }

    /**
     * Register hooks.
     */
    public static function register(): void
    {
        add_action('save_post', [self::class, 'ensureDefaults'], 5, 2);
        add_filter('wp_unique_post_slug', [self::class, 'uniqueSlug'], 10, 6);

        add_filter('post_link', [self::class, 'filterPermalink'], 10, 2);
        add_filter('page_link', [self::class, 'filterPermalink'], 10, 2);
        add_filter('post_type_link', [self::class, 'filterPermalink'], 10, 2);

        add_action('pre_get_posts', [self::class, 'filterArchives']);
    }
}
