<?php
/**
 * Contact form — REST endpoint + admin settings.
 *
 * Endpoint:  POST /wp-json/snel/v1/contact
 * Admin:     Snelstack → Contact  (set webhook URL)
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

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
        ],
    ]);
});

function snel_contact_submit(WP_REST_Request $request): WP_REST_Response
{
    $webhook = get_option('snel_contact_webhook', '');

    if (! $webhook) {
        return new WP_REST_Response(
            ['message' => 'Contactformulier is nog niet geconfigureerd. Stel een webhook in via Snelstack → Contact.'],
            500
        );
    }

    $payload = [
        'name'      => $request->get_param('name'),
        'email'     => $request->get_param('email'),
        'phone'     => $request->get_param('phone') ?: null,
        'message'   => $request->get_param('message'),
        'source'    => get_bloginfo('url'),
        'timestamp' => current_time('c'),
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

    return new WP_REST_Response(['message' => 'Bedankt! We nemen zo snel mogelijk contact op.'], 200);
}

// ---------------------------------------------------------------------------
// Admin — submenu under Snelstack
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
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Opgeslagen.', 'snel') . '</p></div>';
    }

    $webhook = get_option('snel_contact_webhook', '');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Contact Instellingen', 'snel'); ?></h1>
        <p style="color:#666;margin-bottom:24px;">
            <?php esc_html_e('Formulierinzendingen worden als JSON naar de webhook gestuurd.', 'snel'); ?>
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
                            <?php esc_html_e('n8n, Make, Zapier of een andere webhook. JSON payload: name, email, phone, message, source, timestamp.', 'snel'); ?>
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
            </table>
            <?php submit_button(__('Opslaan', 'snel')); ?>
        </form>
    </div>
    <?php
}
