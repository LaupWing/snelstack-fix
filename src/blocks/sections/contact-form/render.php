<?php
/**
 * Snel Contact Form — name, email, phone, service, message.
 *
 * Posts JSON to /wp-json/snel/v1/contact, which stores the lead and forwards it
 * to the webhook URL configured under Snelstack → Contact in the admin.
 *
 * The service chips are built from the `service` CPT (current language), can be
 * preselected with ?dienst=<slug>, and always include a "weet ik nog niet"
 * option. After a successful submit the visitor goes to the thank-you page when
 * one is configured; otherwise the inline confirmation is shown.
 *
 * @var array $attributes
 */

defined('ABSPATH') || exit;

$action = rest_url('snel/v1/contact');
$nonce  = wp_create_nonce('wp_rest');

$input_cls    = 'snel-cf-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition';
$label_cls    = 'block text-sm font-medium text-slate-700 mb-1.5';

// Service picker — options come from the `service` CPT, in the current language.
$services = function_exists('snel_contact_service_options') ? snel_contact_service_options() : [];

// Preselect from ?dienst=<slug> (view.js re-applies this client-side, so a
// full-page cache can never serve a stale selection).
$preselect = isset($_GET['dienst']) ? sanitize_title(wp_unslash($_GET['dienst'])) : '';
$slugs     = array_column($services, 'slug');
if ($preselect !== '' && ! in_array($preselect, $slugs, true)) {
    $preselect = '';
}

// Redirect to the thank-you page after a successful submit. Empty = stay on the
// form and show the inline confirmation (block setting, or no page configured).
$redirect = ! empty($attributes['redirect']) && function_exists('snel_contact_thankyou_url')
    ? snel_contact_thankyou_url()
    : '';
?>
<section data-seo-content class="bg-white <?php echo snel_section_padding(['size' => 'md', 'disableTop' => true]); ?>">
    <div class="mx-auto w-full max-w-5xl px-4 md:px-8">
        <form
            class="snel-contact-form mx-auto max-w-2xl"
            data-action="<?php echo esc_url($action); ?>"
            data-nonce="<?php echo esc_attr($nonce); ?>"
            data-lang="<?php echo esc_attr(snel_get_lang()); ?>"
            data-error="<?php echo esc_attr(snel__('Er is iets misgegaan. Probeer het opnieuw.')); ?>"
            data-success="<?php echo esc_attr(snel__('Bedankt! We nemen zo snel mogelijk contact op.')); ?>"
            <?php if ($redirect) : ?>data-redirect="<?php echo esc_url($redirect); ?>"<?php endif; ?>
            novalidate
        >
            <div class="space-y-6">

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="snel-cf-name" class="<?php echo $label_cls; ?>"><?php echo esc_html(snel__('Naam')); ?> <span class="text-brand-primary">*</span></label>
                        <input
                            id="snel-cf-name"
                            name="name"
                            type="text"
                            required
                            autocomplete="name"
                            placeholder="<?php echo esc_attr(snel__('Jan de Vries')); ?>"
                            class="<?php echo $input_cls; ?>"
                        />
                    </div>
                    <div>
                        <label for="snel-cf-email" class="<?php echo $label_cls; ?>"><?php echo esc_html(snel__('E-mailadres')); ?> <span class="text-brand-primary">*</span></label>
                        <input
                            id="snel-cf-email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="<?php echo esc_attr(snel__('jan@bedrijf.nl')); ?>"
                            class="<?php echo $input_cls; ?>"
                        />
                    </div>
                </div>

                <div>
                    <label for="snel-cf-phone" class="<?php echo $label_cls; ?>"><?php echo esc_html(snel__('Telefoonnummer')); ?> <span class="text-slate-400 font-normal"><?php echo esc_html(snel__('(optioneel)')); ?></span></label>
                    <input
                        id="snel-cf-phone"
                        name="phone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="<?php echo esc_attr(snel__('+31 6 12 34 56 78')); ?>"
                        class="<?php echo $input_cls; ?>"
                    />
                </div>

                <?php if ($services) : ?>
                <div>
                    <label for="snel-cf-service" class="<?php echo $label_cls; ?>">
                        <?php echo esc_html(snel__('Waar gaat het over?')); ?>
                        <span class="text-slate-400 font-normal"><?php echo esc_html(snel__('(optioneel)')); ?></span>
                    </label>
                    <div class="relative">
                        <select id="snel-cf-service" name="service" class="<?php echo $input_cls; ?> snel-cf-select cursor-pointer appearance-none pr-10">
                            <?php foreach ($services as $service) :
                                // Default: the URL choice, else "weet ik nog niet" (the empty slug).
                                $selected = $preselect !== ''
                                    ? $service['slug'] === $preselect
                                    : $service['slug'] === '';
                                $label = trim(($service['icon'] ? $service['icon'] . '  ' : '') . $service['label']);
                                ?>
                                <option value="<?php echo esc_attr($service['slug']); ?>" <?php selected($selected); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="pointer-events-none absolute right-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <label for="snel-cf-message" class="<?php echo $label_cls; ?>"><?php echo esc_html(snel__('Bericht')); ?> <span class="text-brand-primary">*</span></label>
                    <textarea
                        id="snel-cf-message"
                        name="message"
                        required
                        rows="5"
                        placeholder="<?php echo esc_attr(snel__('Vertel ons over jouw project, idee of vraag...')); ?>"
                        class="<?php echo $input_cls; ?> resize-none"
                    ></textarea>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <span class="relative inline-flex">
                        <span class="snel-btn-glow" aria-hidden="true"></span>
                    <button
                        type="submit"
                        class="snel-cf-submit group relative inline-flex h-12 cursor-pointer overflow-hidden rounded-full p-[3px] transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50"
                    >
                        <span class="snel-gradient-ring absolute inset-0 rounded-full"></span>
                        <span
                            class="snel-cf-btn-label relative inline-flex flex-1 items-center justify-center whitespace-nowrap rounded-full bg-white px-6 text-base font-semibold text-gray-900"
                            data-sending="<?php echo esc_attr(snel__('Versturen…')); ?>"
                        ><?php echo esc_html(snel__('Verstuur bericht')); ?></span>
                    </button>
                    </span>

                    <p class="snel-cf-status hidden text-sm font-medium" aria-live="polite"></p>
                </div>

                <p class="text-sm text-slate-500">
                    <?php echo esc_html(snel__('Liever direct een gesprek inplannen?')); ?>
                    <a href="https://calendly.com/snelstack/30min" target="_blank" rel="noopener"
                       class="font-medium text-brand-primary underline decoration-brand-primary/30 underline-offset-4 transition-colors hover:decoration-brand-primary"><?php echo esc_html(snel__('Klik hier')); ?></a>
                </p>

            </div>
        </form>
    </div>
</section>
