<?php

/**
 * Snel Lead Demo — the demo CTA on the Lead Automation landing page.
 *
 * Two channel buttons (e-mail / WhatsApp) reveal a short form. The form posts
 * to the EXISTING contact endpoint: POST /wp-json/snel/v1/contact, which
 * forwards the JSON to the webhook set under Snelstack → Contact.
 *
 * Endpoint contract (inc/contact/index.php): name, email and message are
 * required; phone, channel, source_block, lang and page are optional and are
 * stored + forwarded to the webhook. This block sends channel=email|whatsapp
 * and source_block=lead-demo so n8n can pick the follow-up route. The chosen
 * channel is ALSO spelled out in `message`, so a webhook that only reads the
 * classic fields still gets it.
 *
 * The endpoint returns a `redirect` (thank-you page) on success — this block
 * deliberately IGNORES it and shows an inline confirmation instead: this is a
 * one-pager whose whole point is that there is no exit.
 *
 * Animations are transform/opacity only — no backdrop-filter over the page.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

$uid       = wp_unique_id('snel-ld-');
$heading   = $attributes['heading']       ?? '';
$body      = $attributes['body']          ?? '';
$lbl_mail  = $attributes['emailLabel']    ?? '';
$lbl_wa    = $attributes['whatsappLabel'] ?? '';

$action = rest_url('snel/v1/contact');
$nonce  = wp_create_nonce('wp_rest');

$mail_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="size-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>';

$wa_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>';
?>
<section data-seo-content class="snel-lead-demo relative <?php echo esc_attr(snel_section_class($attributes)); ?>"<?php echo snel_section_style($attributes); ?>>
	<div class="mx-auto w-full max-w-3xl px-4 md:px-8 <?php echo snel_section_padding($attributes); ?>">

		<?php if ($heading) : ?>
			<h2 class="snel-heading snel-h-2xl"><?php echo wp_kses_post($heading); ?></h2>
		<?php endif; ?>

		<?php if ($body) : ?>
			<p class="snel-text snel-text-lg mt-5 max-w-2xl"><?php echo wp_kses_post($body); ?></p>
		<?php endif; ?>

		<div class="mt-8 flex flex-wrap items-center gap-3">
			<button
				type="button"
				class="snel-ld-choice group"
				data-channel="email"
				aria-pressed="false"
				aria-controls="<?php echo esc_attr($uid); ?>-panel"
			>
				<?php echo $mail_icon; ?>
				<span class="whitespace-nowrap font-medium"><?php echo esc_html($lbl_mail); ?></span>
			</button>

			<button
				type="button"
				class="snel-ld-choice group"
				data-channel="whatsapp"
				aria-pressed="false"
				aria-controls="<?php echo esc_attr($uid); ?>-panel"
			>
				<?php echo $wa_icon; ?>
				<span class="whitespace-nowrap font-medium"><?php echo esc_html($lbl_wa); ?></span>
			</button>
		</div>

		<div id="<?php echo esc_attr($uid); ?>-panel" class="snel-ld-panel mt-8" hidden>
			<form
				class="snel-ld-form"
				data-action="<?php echo esc_url($action); ?>"
				data-nonce="<?php echo esc_attr($nonce); ?>"
				data-lang="<?php echo esc_attr(snel_get_lang()); ?>"
				data-message-email="<?php echo esc_attr(snel__('Demo-aanvraag Lead Automation. Gewenst kanaal: e-mail.')); ?>"
				data-message-whatsapp="<?php echo esc_attr(snel__('Demo-aanvraag Lead Automation. Gewenst kanaal: WhatsApp.')); ?>"
				data-label-send="<?php echo esc_attr(snel__('Start de demo')); ?>"
				data-label-sending="<?php echo esc_attr(snel__('Versturen…')); ?>"
				data-msg-success="<?php echo esc_attr(snel__('Gelukt. Je krijgt binnen enkele minuten een reactie, precies zoals jouw leads die zouden krijgen.')); ?>"
				data-msg-error="<?php echo esc_attr(snel__('Er is iets misgegaan. Probeer het opnieuw.')); ?>"
				novalidate
			>
				<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
					<div>
						<label class="snel-ld-label" for="<?php echo esc_attr($uid); ?>-name">
							<?php echo esc_html(snel__('Naam')); ?> <span class="text-pink-400">*</span>
						</label>
						<input
							id="<?php echo esc_attr($uid); ?>-name"
							name="name"
							type="text"
							required
							autocomplete="name"
							placeholder="<?php echo esc_attr(snel__('Jan de Vries')); ?>"
							class="snel-ld-input"
						/>
					</div>

					<div>
						<label class="snel-ld-label" for="<?php echo esc_attr($uid); ?>-email">
							<?php echo esc_html(snel__('E-mailadres')); ?> <span class="text-pink-400">*</span>
						</label>
						<input
							id="<?php echo esc_attr($uid); ?>-email"
							name="email"
							type="email"
							required
							autocomplete="email"
							placeholder="<?php echo esc_attr(snel__('jan@bedrijf.nl')); ?>"
							class="snel-ld-input"
						/>
					</div>
				</div>

				<div class="snel-ld-field-phone mt-5" hidden>
					<label class="snel-ld-label" for="<?php echo esc_attr($uid); ?>-phone">
						<?php echo esc_html(snel__('WhatsApp-nummer')); ?> <span class="text-pink-400">*</span>
					</label>
					<input
						id="<?php echo esc_attr($uid); ?>-phone"
						name="phone"
						type="tel"
						autocomplete="tel"
						placeholder="<?php echo esc_attr(snel__('+31 6 12 34 56 78')); ?>"
						class="snel-ld-input"
					/>
				</div>

				<div class="mt-7 flex flex-col gap-4 sm:flex-row sm:items-center">
					<span class="relative inline-flex">
						<span class="snel-btn-glow" aria-hidden="true"></span>
						<button
							type="submit"
							class="snel-ld-submit group relative inline-flex h-12 cursor-pointer overflow-hidden rounded-full p-[3px] transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50"
						>
							<span class="snel-gradient-ring absolute inset-0 rounded-full"></span>
							<span class="snel-ld-submit-label relative inline-flex flex-1 items-center justify-center whitespace-nowrap rounded-full bg-white px-6 text-base font-semibold text-gray-900">
								<?php echo esc_html(snel__('Start de demo')); ?>
							</span>
						</button>
					</span>

					<p class="snel-ld-status text-sm font-medium" role="status" aria-live="polite" hidden></p>
				</div>
			</form>
		</div>

	</div>
</section>
