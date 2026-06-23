<?php
/**
 * Cases CPT — portfolio/case-study pieces.
 *
 * Meta:
 *   _case_client   — client name (string)
 *   _case_services — services delivered (array, stored serialized)
 *   _case_result   — one-line result/tagline for card previews (string)
 *   _case_url      — live site URL (string)
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

// ---------------------------------------------------------------------------
// CPT registration
// ---------------------------------------------------------------------------

add_action('init', function () {
    register_post_type('case', [
        'labels' => [
            'name'               => __('Cases', 'snel'),
            'singular_name'      => __('Case', 'snel'),
            'add_new_item'       => __('Nieuwe case', 'snel'),
            'edit_item'          => __('Case bewerken', 'snel'),
            'view_item'          => __('Case bekijken', 'snel'),
            'search_items'       => __('Cases zoeken', 'snel'),
            'not_found'          => __('Geen cases gevonden', 'snel'),
            'not_found_in_trash' => __('Geen cases in prullenbak', 'snel'),
        ],
        'public'            => true,
        'has_archive'       => true,
        'show_in_rest'      => true,
        'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon'         => 'dashicons-portfolio',
        'menu_position'     => 5,
        'rewrite'           => ['slug' => 'cases'],
    ]);
});

// ---------------------------------------------------------------------------
// Meta box
// ---------------------------------------------------------------------------

add_action('add_meta_boxes', function () {
    add_meta_box(
        'snel_case_details',
        __('Case details', 'snel'),
        'snel_case_meta_box_render',
        'case',
        'normal',
        'high'
    );
});

function snel_case_meta_box_render(WP_Post $post): void
{
    $client   = get_post_meta($post->ID, '_case_client', true);
    $services = get_post_meta($post->ID, '_case_services', true) ?: [];
    $result   = get_post_meta($post->ID, '_case_result', true);
    $url      = get_post_meta($post->ID, '_case_url', true);

    $all_services = [
        'WordPress',
        'Design',
        'SEO',
        'AI / Automatisering',
        'n8n',
        'Laravel',
    ];

    wp_nonce_field('snel_case_save', 'snel_case_nonce');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="case_client"><?php _e('Klant', 'snel'); ?></label></th>
            <td><input type="text" id="case_client" name="case_client" value="<?php echo esc_attr($client); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label><?php _e('Services', 'snel'); ?></label></th>
            <td>
                <?php foreach ($all_services as $service) : ?>
                    <label style="display:inline-block;margin-right:16px;margin-bottom:6px">
                        <input type="checkbox" name="case_services[]" value="<?php echo esc_attr($service); ?>"
                            <?php checked(in_array($service, (array) $services, true)); ?> />
                        <?php echo esc_html($service); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th><label for="case_result"><?php _e('Resultaat (tagline)', 'snel'); ?></label></th>
            <td>
                <input type="text" id="case_result" name="case_result" value="<?php echo esc_attr($result); ?>" class="large-text" />
                <p class="description"><?php _e('Bijv. "+140% organisch verkeer in 3 maanden" — zichtbaar op de kaart.', 'snel'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="case_url"><?php _e('Live URL', 'snel'); ?></label></th>
            <td><input type="url" id="case_url" name="case_url" value="<?php echo esc_attr($url); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

add_action('save_post_case', function (int $post_id): void {
    if (
        ! isset($_POST['snel_case_nonce']) ||
        ! wp_verify_nonce($_POST['snel_case_nonce'], 'snel_case_save') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
    ) {
        return;
    }

    update_post_meta($post_id, '_case_client',   sanitize_text_field($_POST['case_client'] ?? ''));
    update_post_meta($post_id, '_case_result',   sanitize_text_field($_POST['case_result'] ?? ''));
    update_post_meta($post_id, '_case_url',      esc_url_raw($_POST['case_url'] ?? ''));

    $services = array_map('sanitize_text_field', (array) ($_POST['case_services'] ?? []));
    update_post_meta($post_id, '_case_services', $services);
});

// ---------------------------------------------------------------------------
// Helper — fetch cases (used by blocks/templates).
// ---------------------------------------------------------------------------

function snel_get_cases(array $args = []): array
{
    return get_posts(array_merge([
        'post_type'      => 'case',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ], $args));
}
