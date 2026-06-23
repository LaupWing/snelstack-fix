<?php
/**
 * Services CPT — services offered by the agency.
 *
 * Meta:
 *   _service_tagline — one-liner for card previews (string)
 *   _service_icon    — emoji icon (string)
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

// ---------------------------------------------------------------------------
// CPT registration
// ---------------------------------------------------------------------------

add_action('init', function () {
    register_post_type('service', [
        'labels' => [
            'name'               => __('Services', 'snel'),
            'singular_name'      => __('Service', 'snel'),
            'add_new_item'       => __('New service', 'snel'),
            'edit_item'          => __('Edit service', 'snel'),
            'view_item'          => __('View service', 'snel'),
            'search_items'       => __('Search services', 'snel'),
            'not_found'          => __('No services found', 'snel'),
            'not_found_in_trash' => __('No services in trash', 'snel'),
        ],
        'public'         => true,
        'has_archive'    => true,
        'show_in_rest'   => true,
        'supports'       => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon'      => 'dashicons-hammer',
        'menu_position'  => 6,
        'rewrite'        => ['slug' => 'ai-diensten'],
        'template'       => [
            ['snel/intro'],
            ['snel/panel'],
            ['snel/features'],
            ['snel/statement'],
            ['snel/partners'],
            ['snel/content'],
        ],
        'template_lock'  => 'all',
    ]);
});

// ---------------------------------------------------------------------------
// Meta box
// ---------------------------------------------------------------------------

add_action('add_meta_boxes', function () {
    add_meta_box(
        'snel_service_details',
        __('Service details', 'snel'),
        'snel_service_meta_box_render',
        'service',
        'normal',
        'high'
    );
});

function snel_service_meta_box_render(WP_Post $post): void
{
    $tagline  = get_post_meta($post->ID, '_service_tagline', true);
    $icon     = get_post_meta($post->ID, '_service_icon', true);
    $headline = get_post_meta($post->ID, '_service_headline', true);

    wp_nonce_field('snel_service_save', 'snel_service_nonce');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="service_icon"><?php _e('Icon (emoji)', 'snel'); ?></label></th>
            <td>
                <input type="text" id="service_icon" name="service_icon" value="<?php echo esc_attr($icon); ?>" class="small-text" style="font-size:1.5rem;width:60px;text-align:center" />
                <p class="description"><?php _e('E.g. 🚀 💡 🤖 🎨 📈', 'snel'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_tagline"><?php _e('Tagline (pill)', 'snel'); ?></label></th>
            <td>
                <input type="text" id="service_tagline" name="service_tagline" value="<?php echo esc_attr($tagline); ?>" class="large-text" />
                <p class="description"><?php _e('Short label shown in the tag pill. E.g. "Vanaf 2 weken live"', 'snel'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_headline"><?php _e('Big heading', 'snel'); ?></label></th>
            <td>
                <input type="text" id="service_headline" name="service_headline" value="<?php echo esc_attr($headline); ?>" class="large-text" />
                <p class="description"><?php _e('Punchy H1-style headline. E.g. "Verkoop slimmer.\nGroei sneller."', 'snel'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_visual"><?php _e('Right-side visual', 'snel'); ?></label></th>
            <td>
                <?php
                $visual = get_post_meta($post->ID, '_service_visual', true);
                $options = [
                    ''           => 'None',
                    'website'    => 'Browser mockup',
                    'ai'         => 'AI Chat',
                    'automation' => 'n8n Flow',
                    'software'   => 'Code Editor',
                    'vibe'       => 'Vibe Coding',
                    'seo'        => 'SEO Rankings',
                    'retainer'   => 'Uptime Monitor',
                ];
                ?>
                <select id="service_visual" name="service_visual">
                    <?php foreach ($options as $val => $label) : ?>
                    <option value="<?php echo esc_attr($val); ?>"<?php selected($visual, $val); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action('save_post_service', function (int $post_id): void {
    if (
        ! isset($_POST['snel_service_nonce']) ||
        ! wp_verify_nonce($_POST['snel_service_nonce'], 'snel_service_save') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
    ) {
        return;
    }

    update_post_meta($post_id, '_service_tagline',  sanitize_text_field($_POST['service_tagline']  ?? ''));
    update_post_meta($post_id, '_service_icon',     sanitize_text_field($_POST['service_icon']     ?? ''));
    update_post_meta($post_id, '_service_headline', sanitize_text_field($_POST['service_headline'] ?? ''));
    update_post_meta($post_id, '_service_visual',   sanitize_key($_POST['service_visual']   ?? ''));
});

// ---------------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------------

function snel_get_services(array $args = []): array
{
    return get_posts(array_merge([
        'post_type'      => 'service',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ], $args));
}
