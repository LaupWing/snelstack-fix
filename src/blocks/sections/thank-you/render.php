<?php
/**
 * Snel Thank You — the page people land on after sending the contact form.
 *
 * Confirmation + response time + a direct "plan a call" button (Calendly).
 * The redirect itself is handled by the contact form block (data-redirect),
 * which reads the page chosen under Snelstack → Contact.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

$heading   = $attributes['heading']   ?? '';
$paragraph = $attributes['paragraph'] ?? '';

// Empty attributes fall back to the translated theme strings, so a freshly
// inserted block is already correct in both languages.
if ($heading === '')   $heading   = snel__('Bedankt voor je bericht.');
if ($paragraph === '') $paragraph = snel__('Je bericht is binnen. Je hebt binnen één werkdag antwoord.');

$show_calendly = $attributes['showCalendly'] ?? true;
$calendly_url  = $attributes['calendlyUrl'] ?: 'https://calendly.com/snelstack/30min';

$theme     = $attributes['theme'] ?? 'white';
$is_dark   = in_array($theme, ['dark', 'canvas'], true);
$theme_bg  = ['dark' => '#2e1065', 'canvas' => '#020617', 'white' => '#ffffff'][$theme] ?? '#ffffff';
$fade      = ['dark' => 'from-[#2e1065]', 'canvas' => 'from-[#020617]'][$theme] ?? 'from-white';
$theme_cls = $theme === 'canvas' ? 'is-dark bg-canvas' : ($is_dark ? 'is-dark' : 'bg-white');

$calendar_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3.5 shrink-0"><path fill-rule="evenodd" d="M4 1.75a.75.75 0 0 1 1.5 0V3h5V1.75a.75.75 0 0 1 1.5 0V3A2 2 0 0 1 14 5v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2V1.75ZM4.5 6a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7Z" clip-rule="evenodd"/></svg>';
?>
<section data-seo-content class="snel-thanks relative overflow-hidden <?php echo esc_attr($theme_cls); ?>" style="background-color:<?php echo esc_attr($theme_bg); ?>">
	<?php snel_background_open([
		'position' => 'absolute',
		'backdrop' => 'transparent',
		'fade'     => $fade,
		'beams'    => $attributes['showBeams']    ?? true,
		'gradient' => $attributes['showGradient'] ?? true,
	]); ?>

	<div class="px-4 pt-16 pb-20 md:px-8 lg:pt-20">
		<?php snel_panel_open(['dark' => $is_dark, 'inner_class' => 'items-center text-center']); ?>

			<span class="snel-thanks-check mb-8 inline-flex size-14 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/30" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="size-7"><path d="M20 6 9 17l-5-5"/></svg>
			</span>

			<h1 class="snel-heading snel-h-xl max-w-3xl"><?php echo wp_kses_post($heading); ?></h1>

			<p class="snel-text snel-text-lg mt-6 max-w-2xl"><?php echo wp_kses_post($paragraph); ?></p>

			<?php if ($show_calendly) : ?>
				<p class="snel-text mt-10 max-w-2xl"><?php echo esc_html(snel__('Liever niet wachten? Plan meteen een kennismaking van 30 minuten.')); ?></p>

				<div class="mt-5 flex flex-wrap items-center justify-center gap-4">
					<?php get_template_part('template-parts/gradient-button', null, [
						'href'       => $calendly_url,
						'label'      => snel__('Plan een gesprek'),
						'icon'       => $calendar_icon,
						'target'     => '_blank',
						'face_class' => 'px-6 py-3 text-base',
					]); ?>

					<a href="<?php echo esc_url(snel_url(home_url('/'))); ?>"
					   class="snel-text snel-text-sm inline-flex items-center gap-2 rounded-full px-5 py-3 underline-offset-4 transition hover:underline">
						<?php echo esc_html(snel__('Terug naar de homepage')); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="mt-10">
					<a href="<?php echo esc_url(snel_url(home_url('/'))); ?>"
					   class="snel-text snel-text-sm inline-flex items-center gap-2 rounded-full px-5 py-3 underline-offset-4 transition hover:underline">
						<?php echo esc_html(snel__('Terug naar de homepage')); ?>
					</a>
				</div>
			<?php endif; ?>

		<?php snel_panel_close(); ?>
	</div>

	<?php snel_background_close(); ?>
</section>
