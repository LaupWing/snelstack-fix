<?php
/**
 * SEO for the CPT archives.
 *
 * Yoast stores archive titles in one global option, so /cases/ and /en/cases/
 * would share a single string. These filters pick the copy per language
 * instead. Single posts and pages carry their own Yoast meta and are not
 * touched here.
 */

defined('ABSPATH') || exit;

/**
 * @return array{title: string, desc: string}|null
 */
function snel_seo_archive_copy(): ?array
{
    $en = function_exists('snel_get_lang') && snel_get_lang() === 'en';

    if (is_post_type_archive('case')) {
        return $en
            ? [
                'title' => 'Cases: custom software and automation',
                'desc'  => 'Compliance platforms, booking systems and automations that save hours every week. Design, code and automation in one.',
            ]
            : [
                'title' => 'Cases: software op maat en automatisering',
                'desc'  => 'Compliance-platformen, boekingssystemen en automatiseringen die uren per week schelen. Design, code en automatisering in een.',
            ];
    }

    if (is_post_type_archive('service')) {
        return $en
            ? [
                'title' => 'AI services and custom software',
                'desc'  => 'Four ways I take work off your plate, from single automations to complete custom software. You always speak to the builder.',
            ]
            : [
                'title' => 'AI-diensten en software op maat',
                'desc'  => 'Vier manieren waarop ik werk uit handen neem, van losse automatiseringen tot software op maat. Je spreekt altijd de bouwer zelf.',
            ];
    }

    return null;
}

/**
 * Branded share card for the pages that have no featured image of their own:
 * the front page, the blog index, the two archives and the service singles.
 * Returns an absolute URL, or null when the page carries its own image.
 */
function snel_seo_share_card(): ?string
{
    $en   = function_exists('snel_get_lang') && snel_get_lang() === 'en';
    $card = null;

    if (is_front_page()) {
        $card = 'og-home';
    } elseif (is_home()) {
        $card = 'og-blog';
    } elseif (is_post_type_archive('case')) {
        $card = 'og-cases';
    } elseif (is_post_type_archive('service') || is_singular('service')) {
        $card = 'og-diensten';
    } elseif (is_singular('page') && ! has_post_thumbnail()) {
        $card = 'og-home';
    }

    if (! $card) {
        return null;
    }

    return get_template_directory_uri() . '/assets/images/og/' . $card . ($en ? '-en' : '') . '.jpg';
}

add_filter('wpseo_title', function ($title) {
    $copy = snel_seo_archive_copy();

    return $copy ? $copy['title'] . ' - ' . get_bloginfo('name') : $title;
});

add_filter('wpseo_metadesc', function ($desc) {
    $copy = snel_seo_archive_copy();

    return $copy ? $copy['desc'] : $desc;
});

// og:title / og:description follow the same copy, otherwise a shared link shows
// the Yoast default again.
add_filter('wpseo_opengraph_title', function ($title) {
    $copy = snel_seo_archive_copy();

    return $copy ? $copy['title'] . ' - ' . get_bloginfo('name') : $title;
});

add_filter('wpseo_opengraph_desc', function ($desc) {
    $copy = snel_seo_archive_copy();

    return $copy ? $copy['desc'] : $desc;
});

// wpseo_opengraph_image only filters an image Yoast already found; these pages
// have none, so the card has to be added to the image container instead.
add_action('wpseo_add_opengraph_images', function ($images) {
    $card = snel_seo_share_card();
    if ($card) {
        $images->add_image(['url' => $card, 'width' => 1200, 'height' => 630]);
    }
});

add_filter('wpseo_twitter_image', function ($image) {
    return snel_seo_share_card() ?: $image;
});
