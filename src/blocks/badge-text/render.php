<?php
/**
 * Snel Badge — a text pill. Colour, border, bg and weight adapt to the section
 * background via .snel-badge / .is-dark .snel-badge (theme.css).
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

$label = $attributes['label'] ?? '';
$color = $attributes['color'] ?? 'violet';
$allowed = ['teal', 'sky', 'violet', 'pink', 'red'];
$color = in_array($color, $allowed, true) ? $color : 'violet';
?>
<span class="snel-badge snel-badge--<?php echo esc_attr($color); ?> inline-flex h-8 items-center rounded-md px-3 text-sm backdrop-blur-md"><?php echo esc_html($label); ?></span>
