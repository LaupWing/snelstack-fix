<?php
defined('ABSPATH') || exit;

$heading    = $attributes['heading']    ?? '';
$subheading = $attributes['subheading'] ?? '';
$cta_label  = $attributes['ctaLabel']   ?? '';
$cta_url    = $attributes['ctaUrl']     ?? '#';
?>
<section class="relative bg-white px-4 pt-40 pb-20 md:px-8">
    <div class="mx-auto max-w-5xl flex flex-col gap-8">

        <p class="text-sm text-slate-500">Score 4.9 · op basis van 74 reviews</p>

        <?php if ($heading) : ?>
        <h1 class="max-w-4xl font-semibold text-slate-950 text-2xl/tight md:text-3xl/tight lg:text-5xl/tight">
            <?php echo wp_kses($heading, ['em' => [], 'strong' => []]); ?>
        </h1>
        <?php endif; ?>

        <?php if ($subheading) : ?>
        <p class="max-w-2xl text-lg text-slate-600">
            <?php echo wp_kses($subheading, ['em' => [], 'strong' => []]); ?>
        </p>
        <?php endif; ?>

        <?php if ($cta_label) : ?>
        <a href="<?php echo esc_url($cta_url); ?>" class="inline-flex h-12 w-fit items-center rounded-full bg-violet-600 px-6 text-base font-semibold text-white">
            <?php echo esc_html($cta_label); ?>
        </a>
        <?php endif; ?>

    </div>
</section>
