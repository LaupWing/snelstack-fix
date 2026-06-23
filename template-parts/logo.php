<?php
/**
 * Snelstack wordmark logo (markup-based — no image).
 *
 * A gradient circle with a lightning bolt, plus the "snelstack" wordmark
 * ("snel" in a blue→violet gradient, "stack" in serif italic).
 *
 * Usage:
 *   get_template_part('template-parts/logo');                          // icon + text
 *   get_template_part('template-parts/logo', null, ['hide_text' => true]); // icon only
 *
 * @package Snel
 */

$hide_text = ! empty($args['hide_text']);
?>
<span class="flex items-center gap-2">
    <span class="flex items-center justify-center rounded-full bg-gradient-to-br from-sky-400 to-violet-500 p-1.5">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
    </span>
    <?php if (! $hide_text) : ?>
        <span class="text-xl tracking-tight">
            <span class="bg-gradient-to-r from-sky-400 to-violet-500 bg-clip-text font-bold text-transparent">snel</span><span class="font-serif italic text-gray-900">stack</span>
        </span>
    <?php endif; ?>
</span>
