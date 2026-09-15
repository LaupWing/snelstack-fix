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
