<?php
/**
 * Hero section — static template part.
 * Mirrors the snel/intro block output exactly.
 *
 * ACF fields (group: Hero, location: front page / service pages):
 *   eyebrow_type        select  review|text|none
 *   eyebrow_text        text    shown when type = text
 *   heading             text    <em> for muted words
 *   cta_primary_text    text
 *   cta_primary_url     url
 *   cta_secondary_text  text
 *   cta_secondary_url   url
 */
defined('ABSPATH') || exit;

$eyebrow_type = get_field('eyebrow_type')       ?: 'none';
$eyebrow_text = get_field('eyebrow_text')       ?: '';
$heading      = get_field('heading')            ?: '';
$cta_1_text   = get_field('cta_primary_text')   ?: '';
$cta_1_url    = get_field('cta_primary_url')    ?: '#';
$cta_2_text   = get_field('cta_secondary_text') ?: '';
$cta_2_url    = get_field('cta_secondary_url')  ?: '#';

?>
<section data-seo-content class="snel-hero relative">
    <?php snel_background_open(['position' => 'absolute', 'backdrop' => 'white']); ?>
        <div class="px-4 pt-40 pb-20 md:px-8 lg:pt-44">
            <?php snel_panel_open(['inner_class' => 'gap-8 xl:gap-12']); ?>

                <?php if ($eyebrow_type === 'review') : ?>
                <div>
                    <span class="inline-flex h-8 items-center gap-3 rounded-md border border-white/40 bg-white/50 px-2.5 text-sm font-medium shadow-sm backdrop-blur-md">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-slate-950">Score 4.9</span>
                        <span class="flex gap-1">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                            <svg viewBox="0 0 20 20" fill="#fbbf24" class="h-4 w-4"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.36 4.18a1 1 0 0 0 .95.69h4.4c.96 0 1.36 1.23.58 1.8l-3.56 2.59a1 1 0 0 0-.36 1.12l1.36 4.18c.3.92-.76 1.69-1.54 1.12l-3.56-2.59a1 1 0 0 0-1.18 0l-3.56 2.59c-.78.57-1.84-.2-1.54-1.12l1.36-4.18a1 1 0 0 0-.36-1.12L2.4 9.6c-.78-.57-.38-1.8.58-1.8h4.4a1 1 0 0 0 .95-.69l1.36-4.18z"/></svg>
                            <?php endfor; ?>
                        </span>
                        <span class="hidden text-slate-950 md:inline">op basis van 74 reviews</span>
                    </span>
                </div>
                <?php elseif ($eyebrow_type === 'text' && $eyebrow_text) : ?>
                <div>
                    <span class="inline-flex h-8 items-center gap-2 rounded-md border border-white/40 bg-white/50 px-2.5 text-sm font-medium shadow-sm backdrop-blur-md text-slate-950">
                        <?php echo esc_html($eyebrow_text); ?>
                    </span>
                </div>
                <?php endif; ?>

                <?php if ($heading) : ?>
                <h1 class="snel-heading max-w-4xl snel-h-xl font-semibold text-slate-950 text-2xl/tight md:text-3xl/tight lg:text-4xl/tight xl:text-5xl/tight">
                    <?php echo wp_kses($heading, ['em' => [], 'strong' => [], 'span' => ['class' => []]]); ?>
                </h1>
                <?php endif; ?>

                <?php if ($cta_1_text || $cta_2_text) : ?>
                <div class="flex flex-wrap items-center gap-4">
                    <?php if ($cta_1_text) : ?>
                    <?php get_template_part('template-parts/gradient-button', null, [
                        'href'        => $cta_1_url,
                        'label'       => $cta_1_text,
                        'outer_class' => 'h-12',
                        'face_class'  => 'px-6 text-base',
                        'icon'        => '<span class="relative block size-4 overflow-hidden"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 transition-transform duration-300 ease-out group-hover:translate-x-[200%]"><path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 translate-y-[150%] transition-transform duration-300 ease-out group-hover:translate-y-0"><path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/></svg></span>',
                    ]); ?>
                    <?php endif; ?>

                    <?php if ($cta_2_text) : ?>
                    <?php
                    $arrow_path = '<path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/>';
                    ?>
                    <a href="<?php echo esc_url($cta_2_url); ?>" class="group inline-flex h-[46px] items-center gap-2 rounded-md px-4 transition-all duration-300 border-2 border-brand-primary/40 bg-white text-brand-primary hover:bg-brand-primary hover:text-white">
                        <span class="relative block size-4 overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 transition-transform duration-300 ease-out group-hover:translate-x-[200%]"><?php echo $arrow_path; ?></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 translate-y-[150%] transition-transform duration-300 ease-out group-hover:translate-y-0"><?php echo $arrow_path; ?></svg>
                        </span>
                        <span class="whitespace-nowrap font-medium"><?php echo esc_html($cta_2_text); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php snel_panel_close(); ?>
        </div>
    <?php snel_background_close(); ?>
</section>
