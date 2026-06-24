<?php
/**
 * Snel Contact Form — name, email, phone, message.
 *
 * Posts JSON to /wp-json/snel/v1/contact which forwards to the webhook URL
 * configured under Snelstack → Contact in the admin.
 *
 * @var array $attributes
 */

defined('ABSPATH') || exit;

$action = rest_url('snel/v1/contact');
$nonce  = wp_create_nonce('wp_rest');

$input_cls    = 'snel-cf-input w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition';
$label_cls    = 'block text-sm font-medium text-slate-700 mb-1.5';
?>
<section data-seo-content class="bg-white <?php echo snel_section_padding(['size' => 'md', 'disableTop' => true]); ?>">
    <div class="mx-auto w-full max-w-5xl px-4 md:px-8">
        <form
            class="snel-contact-form mx-auto max-w-2xl"
            data-action="<?php echo esc_url($action); ?>"
            data-nonce="<?php echo esc_attr($nonce); ?>"
            novalidate
        >
            <div class="space-y-6">

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="snel-cf-name" class="<?php echo $label_cls; ?>">Naam <span class="text-brand-primary">*</span></label>
                        <input
                            id="snel-cf-name"
                            name="name"
                            type="text"
                            required
                            autocomplete="name"
                            placeholder="Jan de Vries"
                            class="<?php echo $input_cls; ?>"
                        />
                    </div>
                    <div>
                        <label for="snel-cf-email" class="<?php echo $label_cls; ?>">E-mailadres <span class="text-brand-primary">*</span></label>
                        <input
                            id="snel-cf-email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="jan@bedrijf.nl"
                            class="<?php echo $input_cls; ?>"
                        />
                    </div>
                </div>

                <div>
                    <label for="snel-cf-phone" class="<?php echo $label_cls; ?>">Telefoonnummer <span class="text-slate-400 font-normal">(optioneel)</span></label>
                    <input
                        id="snel-cf-phone"
                        name="phone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="+31 6 12 34 56 78"
                        class="<?php echo $input_cls; ?>"
                    />
                </div>

                <div>
                    <label for="snel-cf-message" class="<?php echo $label_cls; ?>">Bericht <span class="text-brand-primary">*</span></label>
                    <textarea
                        id="snel-cf-message"
                        name="message"
                        required
                        rows="5"
                        placeholder="Vertel ons over jouw project, idee of vraag..."
                        class="<?php echo $input_cls; ?> resize-none"
                    ></textarea>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button
                        type="submit"
                        class="snel-cf-submit group relative inline-flex h-12 animate-glow-pulse cursor-pointer overflow-hidden rounded-full p-[3px] transition-transform hover:scale-[1.02] active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50"
                    >
                        <span class="snel-gradient-ring absolute inset-0 rounded-full"></span>
                        <span class="snel-cf-btn-label relative inline-flex items-center justify-center whitespace-nowrap rounded-full bg-white px-6 text-base font-semibold text-gray-900">
                            Verstuur bericht
                        </span>
                    </button>

                    <p class="snel-cf-status hidden text-sm font-medium" aria-live="polite"></p>
                </div>

            </div>
        </form>
    </div>
</section>
