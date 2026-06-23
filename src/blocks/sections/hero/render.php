<?php
defined('ABSPATH') || exit;

$heading    = $attributes['heading']    ?? '';
$subheading = $attributes['subheading'] ?? '';
$image_url  = $attributes['imageUrl']   ?? '';
$cta_label  = $attributes['ctaLabel']   ?? '';
$cta_url    = $attributes['ctaUrl']     ?? '#';
?>
<section class="bg-white">
    <div class="mx-auto max-w-5xl px-6 py-32 flex flex-col gap-8 md:flex-row md:items-center md:gap-12">

        <div class="flex flex-col gap-6 md:w-1/2">
            <?php if ($heading) : ?>
            <h1 class="text-4xl font-bold text-slate-900 lg:text-5xl">
                <?php echo wp_kses($heading, ['em' => [], 'strong' => []]); ?>
            </h1>
            <?php endif; ?>

            <?php if ($subheading) : ?>
            <p class="text-lg text-slate-600">
                <?php echo wp_kses($subheading, ['em' => [], 'strong' => []]); ?>
            </p>
            <?php endif; ?>

            <?php if ($cta_label) : ?>
            <div>
                <a href="<?php echo esc_url($cta_url); ?>" class="inline-flex h-11 items-center rounded-full bg-violet-600 px-6 font-semibold text-white transition-colors hover:bg-violet-700">
                    <?php echo esc_html($cta_label); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($image_url) : ?>
        <div class="md:w-1/2">
            <img src="<?php echo esc_url($image_url); ?>" alt="" class="w-full rounded-2xl object-cover" loading="eager">
        </div>
        <?php endif; ?>

    </div>
</section>
