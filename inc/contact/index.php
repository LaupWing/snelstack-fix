<?php
/**
 * Contact form — REST endpoint, submission storage + admin settings.
 *
 * Endpoint:  POST /wp-json/snel/v1/contact
 * Admin:     Snelstack → Contact       (webhook URL + thank-you page)
 *            Snelstack → Inzendingen   (stored submissions, CPT snel_submission)
 *
 * Flow: the form posts JSON here → the submission is stored as a snel_submission
 * post (never lose a lead, even when the webhook is down) → the payload is
 * forwarded to the configured webhook (n8n/Make/Zapier), which is what actually
 * notifies Loc. The chosen service travels in both.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

const SNEL_CONTACT_CPT = 'snel_submission';

// ---------------------------------------------------------------------------
// Service choices — read from the `service` CPT, never hardcoded
// ---------------------------------------------------------------------------

/**
 * The options for the "which service is this about?" picker, in the current
 * language. Shape: [ ['slug' => …, 'label' => …, 'icon' => …], … ].
 *
 * The last entry is always the "don't know yet" escape hatch (slug '').
 */
function snel_contact_service_options(): array
{
    $options = [];

    foreach (snel_get_services() as $service) {
        $options[] = [
            'slug'  => $service->post_name,
            'label' => get_the_title($service->ID),
            'icon'  => (string) get_post_meta($service->ID, '_service_icon', true),
        ];
    }

    $options[] = [
        'slug'  => '',
        'label' => snel__('Weet ik nog niet'),
        'icon'  => '',
    ];

    return $options;
}

/**
 * Human-readable label for a submitted service slug. Resolved server-side so a
 * spoofed label can never reach the notification; unknown slugs fall back to
 * the raw slug so nothing silently disappears.
 */
function snel_contact_service_label(string $slug): string
{
    if ($slug === '') {
        return snel__('Weet ik nog niet');
    }

    $posts = get_posts([
        'post_type'        => 'service',
        'name'             => $slug,
        'post_status'      => 'publish',
        'numberposts'      => 1,
        'suppress_filters' => true,
    ]);

    return $posts ? get_the_title($posts[0]->ID) : $slug;
}

/**
 * Contact-page URL with a service preselected: /contact/?dienst=<slug>.
 *
 * Use this for any "neem contact op" link on a service page — the form then
 * opens with that service already chosen. Pass a service post (or id); omit it
 * on a single-service page to use the service being viewed.
 */
function snel_contact_url_for_service($service = null): string
{
    $post = get_post($service ?: get_the_ID());
    $url  = snel_url(home_url('/contact/'));

    if (! $post || $post->post_type !== 'service') {
        return $url;
    }

    return add_query_arg('dienst', $post->post_name, $url);
}

// ---------------------------------------------------------------------------
// Thank-you page
// ---------------------------------------------------------------------------

/**
 * Permalink of the thank-you page for a language, or '' when none is set.
 *
 * The option stores the default-language page id; siblings are resolved through
 * the translation group, so /bedankt/ and /en/thank-you/ both work.
 */
function snel_contact_thankyou_url(?string $lang = null): string
{
    $page_id = (int) get_option('snel_contact_thankyou_page', 0);
    if (! $page_id) {
        return '';
    }

    $lang = $lang ?: snel_get_lang();

    if (function_exists('snel_get_translation')) {
        $sibling = (int) snel_get_translation($page_id, $lang);
        if ($sibling) {
            $page_id = $sibling;
        }
    }

    $url = get_permalink($page_id);

    return $url && get_post_status($page_id) === 'publish' ? $url : '';
}

// ---------------------------------------------------------------------------
// Submissions CPT — leads stored in WP, visible under Snelstack
// ---------------------------------------------------------------------------

add_action('init', function () {
    register_post_type(SNEL_CONTACT_CPT, [
        'labels' => [
            'name'          => __('Inzendingen', 'snel'),
            'singular_name' => __('Inzending', 'snel'),
            'menu_name'     => __('Inzendingen', 'snel'),
            'not_found'     => __('Nog geen inzendingen.', 'snel'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => 'snelstack',
        'show_in_rest'        => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'has_archive'         => false,
        'rewrite'             => false,
        'supports'            => ['title'],
        'capabilities'        => [
            'create_posts' => 'do_not_allow', // inbox, not an editor
        ],
        'map_meta_cap'        => true,
    ]);
});

/**
 * Store one submission. Returns the post id (0 on failure).
 */
function snel_contact_store_submission(array $data): int
{
    $title = trim($data['name'] . ' — ' . $data['service_label']);

    $post_id = wp_insert_post([
        'post_type'    => SNEL_CONTACT_CPT,
        'post_status'  => 'publish',
        'post_title'   => $title !== '' ? $title : __('Inzending', 'snel'),
        'post_content' => $data['message'],
    ], true);

    if (is_wp_error($post_id)) {
        return 0;
    }

    foreach ([
        '_snel_cf_email'        => $data['email'],
        '_snel_cf_phone'        => $data['phone'],
        '_snel_cf_service'      => $data['service_label'],
        '_snel_cf_service_slug' => $data['service_slug'],
        '_snel_cf_lang'         => $data['lang'],
        '_snel_cf_page'         => $data['page'],
        '_snel_cf_channel'      => $data['channel'] ?? '',
        '_snel_cf_source_block' => $data['source_block'] ?? '',
    ] as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    return (int) $post_id;
}

// ---------------------------------------------------------------------------
// REST endpoint
// ---------------------------------------------------------------------------

add_action('rest_api_init', function () {
    register_rest_route('snel/v1', '/contact', [
        'methods'             => 'POST',
        'callback'            => 'snel_contact_submit',
        'permission_callback' => '__return_true',
        'args'                => [
            'name'    => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
            'email'   => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_email',
                'validate_callback' => fn ($v) => is_email($v) ?: new WP_Error('invalid_email', 'Ongeldig e-mailadres.'),
            ],
            'phone'   => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
            'message' => ['required' => true,  'sanitize_callback' => 'sanitize_textarea_field'],
            // Closures, not the bare function names: REST passes ($value, $request, $param),
            // and sanitize_title() returns its 2nd arg as fallback on an empty value,
            // which handed the whole WP_REST_Request back as the service.
            'service' => ['required' => false, 'sanitize_callback' => fn ($v) => sanitize_title((string) $v)],
            'lang'    => ['required' => false, 'sanitize_callback' => 'sanitize_key'],
            'page'    => ['required' => false, 'sanitize_callback' => fn ($v) => esc_url_raw((string) $v)],
            // Sent by other forms on the site (e.g. the lead-demo block) so the
            // webhook can route per channel / know which block produced the lead.
            'channel'      => ['required' => false, 'sanitize_callback' => 'sanitize_key'],
            'source_block' => ['required' => false, 'sanitize_callback' => 'sanitize_key'],
        ],
    ]);
});

function snel_contact_submit(WP_REST_Request $request): WP_REST_Response
{
    $service_slug = (string) $request->get_param('service');
    $lang         = (string) $request->get_param('lang');

    if (! $lang || ! in_array($lang, snel_get_supported_langs(), true)) {
        $lang = snel_get_default_lang();
    }

    $data = [
        'name'          => $request->get_param('name'),
        'email'         => $request->get_param('email'),
        'phone'         => (string) $request->get_param('phone'),
        'message'       => $request->get_param('message'),
        'service_slug'  => $service_slug,
        'service_label' => snel_contact_service_label($service_slug),
        'lang'          => $lang,
        'page'          => (string) $request->get_param('page'),
        'channel'       => (string) $request->get_param('channel'),
        'source_block'  => (string) $request->get_param('source_block'),
    ];

    // Store first — a lead in the database beats a lead lost to a dead webhook.
    snel_contact_store_submission($data);

    $redirect = snel_contact_thankyou_url($lang);
    $webhook  = get_option('snel_contact_webhook', '');

    if (! $webhook) {
        return new WP_REST_Response(
            ['message' => 'Contactformulier is nog niet geconfigureerd. Stel een webhook in via Snelstack → Contact.'],
            500
        );
    }

    $payload = [
        'name'         => $data['name'],
        'email'        => $data['email'],
        'phone'        => $data['phone'] ?: null,
        'message'      => $data['message'],
        'service'      => $data['service_label'],   // human-readable, for the notification
        'service_slug' => $data['service_slug'],    // stable key, for routing/filters
        'lang'         => $data['lang'],
        'page'         => $data['page'] ?: null,
        'channel'      => $data['channel'] ?: null,
        'source_block' => $data['source_block'] ?: null,
        'source'       => get_bloginfo('url'),
        'timestamp'    => current_time('c'),
    ];

    $response = wp_remote_post($webhook, [
        'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'    => wp_json_encode($payload),
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        return new WP_REST_Response(['message' => 'Kon bericht niet verzenden. Probeer het opnieuw.'], 500);
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code < 200 || $code >= 300) {
        return new WP_REST_Response(['message' => 'Webhook fout (' . $code . '). Probeer het opnieuw.'], 500);
    }

    return new WP_REST_Response([
        'message'  => 'Bedankt! We nemen zo snel mogelijk contact op.',
        'redirect' => $redirect,
    ], 200);
}

// ---------------------------------------------------------------------------
// Admin — submission list columns + read-only detail
// ---------------------------------------------------------------------------

add_filter('manage_' . SNEL_CONTACT_CPT . '_posts_columns', function (array $columns): array {
    return [
        'cb'      => $columns['cb'] ?? '',
        'title'   => __('Naam', 'snel'),
        'email'   => __('E-mail', 'snel'),
        'phone'   => __('Telefoon', 'snel'),
        'service' => __('Dienst', 'snel'),
        'lang'    => __('Taal', 'snel'),
        'date'    => $columns['date'] ?? __('Datum', 'snel'),
    ];
});

add_action('manage_' . SNEL_CONTACT_CPT . '_posts_custom_column', function (string $column, int $post_id): void {
    $map = [
        'email'   => '_snel_cf_email',
        'phone'   => '_snel_cf_phone',
        'service' => '_snel_cf_service',
        'lang'    => '_snel_cf_lang',
    ];

    if (! isset($map[$column])) {
        return;
    }

    $value = (string) get_post_meta($post_id, $map[$column], true);

    if ($column === 'email' && $value) {
        printf('<a href="mailto:%1$s">%1$s</a>', esc_attr($value));
        return;
    }

    echo $value !== '' ? esc_html($value) : '—';
}, 10, 2);

add_action('add_meta_boxes', function () {
    add_meta_box(
        'snel_submission_details',
        __('Inzending', 'snel'),
        'snel_contact_submission_meta_box',
        SNEL_CONTACT_CPT,
        'normal',
        'high'
    );
});

function snel_contact_submission_meta_box(WP_Post $post): void
{
    $rows = [
        __('E-mail', 'snel')   => get_post_meta($post->ID, '_snel_cf_email', true),
        __('Telefoon', 'snel') => get_post_meta($post->ID, '_snel_cf_phone', true),
        __('Dienst', 'snel')   => get_post_meta($post->ID, '_snel_cf_service', true),
        __('Taal', 'snel')     => get_post_meta($post->ID, '_snel_cf_lang', true),
        __('Pagina', 'snel')   => get_post_meta($post->ID, '_snel_cf_page', true),
        __('Kanaal', 'snel')   => get_post_meta($post->ID, '_snel_cf_channel', true),
        __('Blok', 'snel')     => get_post_meta($post->ID, '_snel_cf_source_block', true),
    ];
    ?>
    <table class="form-table" role="presentation">
        <?php foreach ($rows as $label => $value) : ?>
            <tr>
                <th scope="row"><?php echo esc_html($label); ?></th>
                <td><?php echo $value ? esc_html($value) : '—'; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th scope="row"><?php esc_html_e('Bericht', 'snel'); ?></th>
            <td style="white-space:pre-wrap;max-width:640px;"><?php echo esc_html($post->post_content); ?></td>
        </tr>
    </table>
    <?php
}

// ---------------------------------------------------------------------------
// Admin — settings submenu under Snelstack
// ---------------------------------------------------------------------------

add_action('admin_menu', function () {
    add_submenu_page(
        'snelstack',
        __('Contact', 'snel'),
        __('Contact', 'snel'),
        'manage_options',
        'snel-contact',
        'snel_contact_settings_page'
    );
});

function snel_contact_settings_page(): void
{
    if (
        isset($_POST['snel_contact_nonce'])
        && wp_verify_nonce($_POST['snel_contact_nonce'], 'snel_contact_save')
        && current_user_can('manage_options')
    ) {
        $url = isset($_POST['snel_contact_webhook']) ? esc_url_raw($_POST['snel_contact_webhook']) : '';
        update_option('snel_contact_webhook', $url);
        update_option('snel_contact_thankyou_page', (int) ($_POST['snel_contact_thankyou_page'] ?? 0));
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Opgeslagen.', 'snel') . '</p></div>';
    }

    $webhook  = get_option('snel_contact_webhook', '');
    $thankyou = (int) get_option('snel_contact_thankyou_page', 0);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Contact Instellingen', 'snel'); ?></h1>
        <p style="color:#666;margin-bottom:24px;">
            <?php esc_html_e('Formulierinzendingen worden opgeslagen onder Snelstack → Inzendingen én als JSON naar de webhook gestuurd.', 'snel'); ?>
        </p>

        <form method="post">
            <?php wp_nonce_field('snel_contact_save', 'snel_contact_nonce'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="snel_contact_webhook"><?php esc_html_e('Webhook URL', 'snel'); ?></label>
                    </th>
                    <td>
                        <input
                            type="url"
                            id="snel_contact_webhook"
                            name="snel_contact_webhook"
                            value="<?php echo esc_attr($webhook); ?>"
                            class="regular-text"
                            placeholder="https://jouw-n8n.com/webhook/..."
                        />
                        <p class="description" style="margin-top:8px;max-width:540px;">
                            <?php esc_html_e('n8n, Make, Zapier of een andere webhook. JSON payload: name, email, phone, message, service, service_slug, lang, page, channel, source_block, source, timestamp.', 'snel'); ?>
                        </p>
                        <?php if ($webhook) : ?>
                            <p style="margin-top:8px;color:#059669;font-weight:600;">
                                &#10003; <?php esc_html_e('Webhook geconfigureerd.', 'snel'); ?>
                            </p>
                        <?php else : ?>
                            <p style="margin-top:8px;color:#d63638;font-weight:600;">
                                &#10007; <?php esc_html_e('Geen webhook ingesteld — formulier geeft een foutmelding.', 'snel'); ?>
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="snel_contact_thankyou_page"><?php esc_html_e('Bedankpagina', 'snel'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_dropdown_pages([
                            'name'              => 'snel_contact_thankyou_page',
                            'id'                => 'snel_contact_thankyou_page',
                            'selected'          => $thankyou,
                            'show_option_none'  => __('— Geen (toon melding in het formulier) —', 'snel'),
                            'option_none_value' => 0,
                        ]);
                        ?>
                        <p class="description" style="margin-top:8px;max-width:540px;">
                            <?php esc_html_e('Kies de Nederlandse bedankpagina. Na verzenden stuurt het formulier de bezoeker hierheen; de vertaling (EN) wordt automatisch gekozen via de vertaalkoppeling. Zonder pagina blijft de bezoeker op het formulier met een inline bevestiging.', 'snel'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Opslaan', 'snel')); ?>
        </form>
    </div>
    <?php
}
