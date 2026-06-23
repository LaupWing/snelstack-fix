<?php

/**
 * Core language routing and translation helpers.
 *
 * DO NOT EDIT per project — configure via config/ files instead.
 *
 * @package Snel
 */

if (! defined('ABSPATH')) {
    exit;
}

// Load the LocaleManager class — single source of truth for language config/detection.
require_once get_template_directory() . '/inc/translations/core/LocaleManager.php';

// Load the TranslationGroup class — links a post to its translations (one post per language).
require_once get_template_directory() . '/inc/translations/core/TranslationGroup.php';
TranslationGroup::register();

// Load the TermTranslation class — translated term name/description (shared term).
require_once get_template_directory() . '/inc/translations/core/TermTranslation.php';
TermTranslation::register();

/**
 * Load and cache the languages config.
 *
 * @return array
 */
function snel_get_languages_config()
{
    return LocaleManager::config();
}

/**
 * Get array of supported language codes.
 *
 * @return array e.g. ['nl', 'en']
 */
function snel_get_supported_langs()
{
    return LocaleManager::supported();
}

/**
 * Get the default language code (the one with 'default' => true).
 *
 * @return string e.g. 'nl'
 */
function snel_get_default_lang()
{
    return LocaleManager::default();
}

/**
 * Get the current language from the URL.
 * Falls back to default language if not set.
 *
 * @return string e.g. 'en'
 */
function snel_get_lang()
{
    return LocaleManager::current();
}

/**
 * Check if the current language matches a given language.
 *
 * @param string $lang Language code to check.
 * @return bool
 */
function snel_is_lang($lang)
{
    return LocaleManager::is($lang);
}

// Load the UrlGenerator class — builds language-aware URLs.
require_once get_template_directory() . '/inc/translations/urls/UrlGenerator.php';

// Load and register the Router class — rewrite rules, slug resolution, redirects.
require_once get_template_directory() . '/inc/translations/core/Router.php';
Router::register();

// ---------------------------------------------------------------------------
// URL Helpers
// ---------------------------------------------------------------------------

/**
 * Add the current language prefix to any internal URL.
 *
 * @param string $url Relative URL (e.g., '/contact/' or '/producten/my-item/')
 * @return string
 */
function snel_url($url)
{
    return UrlGenerator::url($url);
}

/**
 * Get the URL for switching to a different language on the current page.
 *
 * @param string $target_lang Language code to switch to (e.g., 'en')
 * @return string Full URL for that language
 */
function snel_lang_url($target_lang)
{
    return UrlGenerator::langUrl($target_lang);
}

// ---------------------------------------------------------------------------
// Translation Helpers
// ---------------------------------------------------------------------------

// Load the Translator class — handles theme string translations and multilingual values.
require_once get_template_directory() . '/inc/translations/core/Translator.php';

/**
 * Save a single theme string translation to the database.
 *
 * @param string $key  The Dutch source text (translation key).
 * @param string $lang Language code.
 * @param string $text Translated text.
 */
function snel_save_translation($key, $lang, $text)
{
    Translator::save($key, $lang, $text);
}

/**
 * Get all theme string translations grouped by section, merging file defaults with DB overrides.
 *
 * @return array ['Section' => ['nl_key' => ['nl' => '...', 'en' => '...', ...]]]
 */
function snel_get_grouped_theme_translations()
{
    return Translator::grouped();
}

/**
 * Translate a static theme string.
 *
 * Usage in templates:
 *   <h1><?php echo snel__('Welkom'); ?></h1>
 *   // Outputs "Welcome" if lang=en
 *
 * @param string $text The default-language (Dutch) text.
 * @return string Translated text, or original if no translation found.
 */
function snel__($text)
{
    return Translator::translate($text);
}

/**
 * Get a post title. Each post is in a single language, so this is just the
 * native title — kept as a thin helper for template compatibility.
 */
function snel_title($post_id = null)
{
    return get_the_title($post_id ?: get_the_ID());
}

function snel_excerpt($post_id = null)
{
    return get_the_excerpt($post_id ?: get_the_ID());
}

// ---------------------------------------------------------------------------
// Translation Group Helpers (one post per language)
// ---------------------------------------------------------------------------

/**
 * The language a post is written in.
 */
function snel_post_lang($post_id = null)
{
    return TranslationGroup::langOf($post_id ?: get_the_ID());
}

/**
 * The translation group id a post belongs to.
 */
function snel_post_group($post_id = null)
{
    return TranslationGroup::groupOf($post_id ?: get_the_ID());
}

/**
 * Find the translation of a post in a given language (0 if none exists).
 */
function snel_get_translation($post_id, $lang)
{
    return TranslationGroup::translation($post_id, $lang);
}

/**
 * All translations of a post, keyed by language: ['nl' => 12, 'en' => 45].
 */
function snel_get_translations($post_id = null)
{
    $post_id = $post_id ?: get_the_ID();
    return TranslationGroup::siblings(TranslationGroup::groupOf($post_id));
}

// ---------------------------------------------------------------------------
// Taxonomy Term Helpers (shared term, translated label/description)
// ---------------------------------------------------------------------------

/**
 * A term's name in the current language, falling back to the native name.
 *
 * Usage in templates:
 *   echo snel_term_name($term);            // current language
 *   echo snel_term_name($term_id, 'en');   // a specific language
 *
 * @param int|WP_Term $term
 */
function snel_term_name($term, $lang = null)
{
    return TermTranslation::name($term, $lang);
}

/**
 * A term's description in the current language, falling back to the native one.
 *
 * @param int|WP_Term $term
 */
function snel_term_description($term, $lang = null)
{
    return TermTranslation::description($term, $lang);
}

// Load and register SEO manager — hreflang, canonical, meta description, html lang.
require_once get_template_directory() . '/inc/translations/seo/SeoManager.php';
SeoManager::register();

// Load AI translation AJAX handler.
require_once get_template_directory() . '/inc/translations/admin/translate.php';

// Load the "Create translation" page-duplication flow (editor sidebar + AJAX).
require_once get_template_directory() . '/inc/translations/admin/create-translation.php';

// Load the admin list-table language column + filter.
require_once get_template_directory() . '/inc/translations/admin/admin-columns.php';

// Load taxonomy term translation (per-language name/description fields + AI button).
require_once get_template_directory() . '/inc/translations/admin/term-translations.php';

// Load nav menu translation (resolves items to their language sibling).
require_once get_template_directory() . '/inc/translations/nav-menu.php';

// ─── Snel SEO Integration ─────────────────────────────────────────────────

/**
 * Provide available languages to Snel SEO plugin.
 */
add_filter( 'snel_seo_languages', function () {
    $config = include get_template_directory() . '/inc/translations/config/languages.php';
    $result = array();
    foreach ( $config as $code => $lang ) {
        $result[] = array(
            'code'    => $code,
            'label'   => $lang['label'],
            'default' => ! empty( $lang['default'] ),
            'locale'  => $lang['locale'] ?? $code,
        );
    }
    return $result;
} );

/**
 * Tell Snel SEO what language the current visitor is viewing.
 */
add_filter( 'snel_seo_current_language', function () {
    return LocaleManager::current();
} );
