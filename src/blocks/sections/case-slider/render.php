<?php
/**
 * Case Slider — full-width image slider with breadcrumb bar.
 *
 * Same frame as snel/thumbnail; slides come from snel/case-slide innerblocks
 * (each an image + optional label/value card). Arrows/dots only with 2+ slides.
 *
 * @var array    $attributes
 * @var string   $content   Rendered snel/case-slide items.
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

if (! trim($content)) {
    return;
}

$post_id    = get_the_ID();
$title      = get_the_title($post_id);
$back_url   = $attributes['backUrl'] ?? '';
$back_label = $attributes['backLabel'] ?? 'Cases';
$bg         = $attributes['bg'] ?? 'white';
$slides     = count($block->parsed_block['innerBlocks'] ?? []);

$chevron_left  = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>';
$chevron_right = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>';
?>
<section class="snel-case-slider <?php echo esc_attr(snel_section_class(['bg' => $bg])); ?>"<?php echo snel_section_style(['bg' => $bg]); ?>>
<div class="mx-auto w-full max-w-7xl px-4 md:px-8 flex flex-col relative gap-4 lg:gap-8">

    <?php // 16:9 image is shorter than the thumbnail's natural height — pull the pill up. ?>
    <?php echo snel_breadcrumb_bar($back_url, $back_label, $title, 'mt-6 md:mt-10'); ?>

    <div class="relative rounded-xl overflow-hidden shadow-inner shadow min-h-[50dvh] w-full bg-slate-900">
        <div class="snel-case-slider-track flex w-full overflow-x-auto snap-x snap-mandatory">
            <?php echo $content; ?>
        </div>

        <?php if ($slides > 1) : ?>
        <div class="absolute top-0 right-0 md:top-auto md:bottom-0 z-10 p-4 lg:p-6 flex items-center gap-3">
            <div class="snel-case-slider-indicator h-0.5 w-16 lg:w-24 overflow-hidden rounded-full bg-white/25">
                <div class="snel-case-slider-bar h-full rounded-full bg-white will-change-transform" style="width:0;transform:translateX(0)"></div>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" class="snel-case-slider-prev flex size-8 lg:size-9 items-center justify-center rounded-full border border-white/10 bg-black/45 text-white transition hover:bg-black/60 disabled:opacity-40 disabled:hover:bg-black/45" aria-label="<?php echo esc_attr(snel__('Vorige')); ?>">
                    <?php echo $chevron_left; ?>
                </button>
                <button type="button" class="snel-case-slider-next flex size-8 lg:size-9 items-center justify-center rounded-full border border-white/10 bg-black/45 text-white transition hover:bg-black/60 disabled:opacity-40 disabled:hover:bg-black/45" aria-label="<?php echo esc_attr(snel__('Volgende')); ?>">
                    <?php echo $chevron_right; ?>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
</section>
