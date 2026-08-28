<?php
/**
 * Case ordering — "Volgorde" subpage under the Cases menu.
 *
 * Drag & drop (jQuery UI Sortable) + up/down arrows, saved into menu_order.
 * Alleen default-taal cases: vertalingen volgen via snel_get_cases(), die op
 * de default-taal query't en daarna naar siblings mapt.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=case',
        __('Volgorde', 'snel'),
        __('Volgorde', 'snel'),
        'manage_options',
        'snel-case-order',
        'snel_case_order_page'
    );
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'case_page_snel-case-order') {
        wp_enqueue_script('jquery-ui-sortable');
    }
});

add_action('wp_ajax_snel_save_case_order', function () {
    check_ajax_referer('snel_case_order_nonce', 'nonce');

    if (! current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $order = isset($_POST['order']) ? array_map('absint', (array) $_POST['order']) : [];
    if (! $order) {
        wp_send_json_error('No order received');
    }

    foreach ($order as $i => $case_id) {
        wp_update_post(['ID' => $case_id, 'menu_order' => $i]);
    }

    wp_send_json_success();
});

function snel_case_order_page(): void
{
    $default = function_exists('snel_get_default_lang') ? snel_get_default_lang() : 'nl';

    $cases = get_posts([
        'post_type'      => 'case',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
        'meta_query'     => [
            'relation' => 'OR',
            ['key' => '_snel_lang', 'value' => $default],
            ['key' => '_snel_lang', 'compare' => 'NOT EXISTS'],
        ],
    ]);

    $nonce = wp_create_nonce('snel_case_order_nonce');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Case volgorde', 'snel'); ?></h1>
        <p><?php esc_html_e('Sleep cases in de gewenste volgorde (of gebruik de pijltjes) en klik op Opslaan. Vertalingen volgen automatisch.', 'snel'); ?></p>

        <div style="margin-bottom:16px">
            <button type="button" id="snel-co-save" class="button button-primary"><?php esc_html_e('Volgorde opslaan', 'snel'); ?></button>
            <span id="snel-co-status" style="margin-left:10px;font-style:italic;color:#666"></span>
        </div>

        <div id="snel-co-list" style="max-width:650px">
            <?php foreach ($cases as $case) :
                $thumb = get_the_post_thumbnail_url($case->ID, 'thumbnail');
            ?>
            <div class="snel-co-item" data-case-id="<?php echo esc_attr($case->ID); ?>">
                <span class="snel-co-handle">&#9776;</span>
                <?php if ($thumb) : ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="" class="snel-co-thumb" />
                <?php else : ?>
                    <span class="snel-co-thumb snel-co-thumb--empty"></span>
                <?php endif; ?>
                <span class="snel-co-title"><?php echo esc_html($case->post_title); ?></span>
                <span class="snel-co-id">#<?php echo esc_html($case->ID); ?></span>
                <div class="snel-co-arrows">
                    <button type="button" class="snel-co-up" title="<?php esc_attr_e('Omhoog', 'snel'); ?>">&#9650;</button>
                    <button type="button" class="snel-co-down" title="<?php esc_attr_e('Omlaag', 'snel'); ?>">&#9660;</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .snel-co-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin-bottom: 4px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .snel-co-handle { color: #999; font-size: 18px; cursor: grab; flex-shrink: 0; }
        .snel-co-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
        .snel-co-thumb--empty { display: inline-block; background: #f0f0f0; }
        .snel-co-title { font-weight: 600; font-size: 14px; flex-grow: 1; }
        .snel-co-id { color: #999; font-size: 12px; flex-shrink: 0; }
        .snel-co-item.ui-sortable-helper { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        #snel-co-list .ui-sortable-placeholder {
            visibility: visible !important;
            border: 2px dashed #b4b9be;
            background: #f7f7f7;
            min-height: 48px;
            margin-bottom: 4px;
            border-radius: 4px;
        }
        .snel-co-arrows { display: flex; flex-direction: column; gap: 2px; flex-shrink: 0; }
        .snel-co-arrows button {
            background: none; border: 1px solid #ccc; border-radius: 3px; cursor: pointer;
            padding: 2px 6px; font-size: 14px; line-height: 1; color: #555;
        }
        .snel-co-arrows button:hover { background: #f0f0f0; border-color: #999; }
        .snel-co-arrows button:disabled { opacity: 0.3; cursor: default; }
    </style>

    <script>
    jQuery(function($) {
        var $list   = $('#snel-co-list');
        var $save   = $('#snel-co-save');
        var $status = $('#snel-co-status');
        var nonce   = '<?php echo esc_js($nonce); ?>';

        $list.sortable({
            handle: '.snel-co-handle',
            placeholder: 'ui-sortable-placeholder',
            tolerance: 'pointer',
            update: updateArrowStates
        });

        $list.on('click', '.snel-co-up', function() {
            var $item = $(this).closest('.snel-co-item');
            var $prev = $item.prev('.snel-co-item');
            if ($prev.length) { $item.insertBefore($prev); updateArrowStates(); }
        });
        $list.on('click', '.snel-co-down', function() {
            var $item = $(this).closest('.snel-co-item');
            var $next = $item.next('.snel-co-item');
            if ($next.length) { $item.insertAfter($next); updateArrowStates(); }
        });

        $save.on('click', function() {
            var order = [];
            $list.find('.snel-co-item').each(function() {
                order.push($(this).data('case-id'));
            });

            $save.prop('disabled', true);
            $status.text('<?php echo esc_js(__('Opslaan...', 'snel')); ?>');

            $.post(ajaxurl, {
                action: 'snel_save_case_order',
                nonce: nonce,
                order: order
            }).done(function(response) {
                $status.text(response.success
                    ? '<?php echo esc_js(__('Volgorde opgeslagen.', 'snel')); ?>'
                    : '<?php echo esc_js(__('Opslaan mislukt.', 'snel')); ?>');
            }).fail(function() {
                $status.text('<?php echo esc_js(__('Opslaan mislukt.', 'snel')); ?>');
            }).always(function() {
                $save.prop('disabled', false);
                setTimeout(function() { $status.text(''); }, 2000);
            });
        });

        function updateArrowStates() {
            var $items = $list.find('.snel-co-item');
            $items.find('.snel-co-up, .snel-co-down').prop('disabled', false);
            $items.first().find('.snel-co-up').prop('disabled', true);
            $items.last().find('.snel-co-down').prop('disabled', true);
        }
        updateArrowStates();
    });
    </script>
    <?php
}
