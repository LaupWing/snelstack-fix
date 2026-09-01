<?php
/**
 * Shared breadcrumb bar — floating pill with Home → parent → current title,
 * plus a "Terug naar X" link. Used by snel/thumbnail and snel/case-slider.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

function snel_breadcrumb_bar(string $back_url, string $back_label, string $title, string $offset = 'mt-10 md:mt-16'): string
{
    if (! $back_url) {
        return '';
    }

    $breadcrumbs = [
        ['label' => snel__('Home'), 'url' => home_url('/')],
        ['label' => $back_label,    'url' => $back_url],
        ['label' => $title,         'url' => ''],
    ];

    ob_start();
    ?>
    <div class="hidden md:block absolute z-10 top-0 left-1/2 -translate-x-1/2 <?php echo esc_attr($offset); ?> w-full max-w-7xl px-8 md:px-12 lg:px-16">
        <div class="flex justify-between py-2 px-3 lg:py-4 lg:px-6 border border-white/10 rounded-full bg-black/45">
            <nav aria-label="Breadcrumb" class="hidden md:flex">
                <ol role="list" class="flex items-center gap-0 text-white/50">
                    <?php foreach ($breadcrumbs as $crumb) : ?>
                        <li class="flex items-center">
                            <?php if ($crumb['url']) : ?>
                                <a href="<?php echo esc_url($crumb['url']); ?>" class="text-sm lg:text-base font-normal hover:text-white transition-colors">
                                    <?php echo esc_html($crumb['label']); ?>
                                </a>
                                <span class="mx-4 text-white/25">/</span>
                            <?php else : ?>
                                <span aria-current="page" class="text-sm lg:text-base font-normal text-white">
                                    <?php echo esc_html($crumb['label']); ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <nav aria-label="Back">
                <a href="<?php echo esc_url($back_url); ?>" class="text-sm lg:text-base font-normal underline-offset-2 hover:underline text-white transition-colors">
                    <?php echo esc_html(sprintf(snel__('Terug naar %s'), $back_label)); ?>
                </a>
            </nav>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
