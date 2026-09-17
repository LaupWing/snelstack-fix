<?php
/**
 * Template Name: Landingspagina (zonder header & footer)
 *
 * Standalone one-pager. Deliberately does NOT call get_header()/get_footer():
 * no site nav, no site footer, so the only way off the page is a CTA.
 *
 * wp_head() / wp_body_open() / wp_footer() are still called, so styles, block
 * view scripts, SEO output and the Snel Translations plugin keep working.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class('antialiased snel-landing'); ?>>
    <?php wp_body_open(); ?>

    <main>
        <?php
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
    </main>

    <?php // Same frosted bar as the site footer, pulled up over the last section's glow. ?>
    <footer class="relative z-50 -mt-12 flex h-12 items-center border-t border-white/10 bg-slate-950/30 px-4 backdrop-blur-sm md:px-8 xl:px-16 2xl:px-32">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 text-xs text-white/40 antialiased sm:text-sm">
            <div class="truncate">&copy;<?php echo esc_html(date_i18n('Y')); ?>&nbsp;&nbsp;·&nbsp;&nbsp;<?php echo esc_html(get_bloginfo('name')); ?></div>
            <div class="shrink-0"><?php echo esc_html(snel__('Lead Automation')); ?></div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>

</html>
