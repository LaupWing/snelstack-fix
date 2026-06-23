<?php
/**
 * Gradient button — the spinning brand-gradient pill (white face, glow pulse).
 *
 * Usage:
 *   get_template_part('template-parts/gradient-button', null, [
 *       'href'       => '#',
 *       'label'      => 'Start je project',
 *       'icon'       => '<svg ...>...</svg>',   // optional, trusted inline SVG
 *       'target'     => '_blank',               // optional
 *       'face_class' => 'px-6 py-3 text-base',  // optional — size of the face
 *   ]);
 *
 * @package Snel
 */

$href        = $args['href'] ?? '#';
$label       = $args['label'] ?? '';
$icon        = $args['icon'] ?? '';
$target      = $args['target'] ?? '';
$face_class  = $args['face_class'] ?? 'px-4 py-2 text-sm md:px-5';
$outer_class = $args['outer_class'] ?? ''; // e.g. 'h-11' to pin total height

$target_attr = $target ? ' target="' . esc_attr($target) . '" rel="noopener noreferrer"' : '';
?>
<a href="<?php echo esc_url($href); ?>" class="inline-flex"<?php echo $target_attr; ?>>
	<span class="group relative inline-flex animate-glow-pulse cursor-pointer overflow-hidden rounded-full p-[3px] transition-transform hover:scale-[1.02] active:scale-[0.98] <?php echo esc_attr($outer_class); ?>">
		<?php // Rotating gradient ring — fills the pill exactly, auto-sizes to any button ?>
		<span class="snel-gradient-ring absolute inset-0 rounded-full"></span>
		<?php // Button face ?>
		<span class="relative inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full bg-white font-semibold text-gray-900 <?php echo esc_attr($face_class); ?>">
			<?php echo $icon; // trusted inline SVG ?>
			<?php echo esc_html($label); ?>
		</span>
	</span>
</a>
