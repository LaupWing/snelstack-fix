<?php
/**
 * Brand button — outline or filled, with animated arrow icon.
 *
 * Usage:
 *   get_template_part('template-parts/button', null, [
 *       'href'    => '/contact',
 *       'label'   => 'Bekijk ons werk',
 *       'variant' => 'outline', // 'outline' (default) or 'filled'
 *       'target'  => '_blank',  // optional
 *   ]);
 *
 * @package Snel
 */

$href    = $args['href']    ?? '#';
$label   = $args['label']   ?? '';
$variant = $args['variant'] ?? 'outline';
$target  = $args['target']  ?? '';

$variant_class = $variant === 'filled'
    ? 'border-2 border-brand-primary bg-brand-primary text-white hover:bg-brand-primary/90'
    : 'border-2 border-brand-primary/40 bg-white text-brand-primary hover:bg-brand-primary hover:text-white';

$target_attr = $target ? ' target="' . esc_attr($target) . '" rel="noopener noreferrer"' : '';

$arrow = '<path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/>';
?>
<a href="<?php echo esc_url($href); ?>" class="group inline-flex h-[46px] items-center gap-2 rounded-md px-4 transition-all duration-300 <?php echo esc_attr($variant_class); ?>"<?php echo $target_attr; ?>>
    <span class="whitespace-nowrap font-medium"><?php echo esc_html($label); ?></span>
    <span class="relative block size-4 overflow-hidden">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 transition-transform duration-300 ease-out group-hover:translate-x-[200%]"><?php echo $arrow; ?></svg>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="absolute inset-0 size-4 translate-y-[150%] transition-transform duration-300 ease-out group-hover:translate-y-0"><?php echo $arrow; ?></svg>
    </span>
</a>
