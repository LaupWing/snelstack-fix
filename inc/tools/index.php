<?php
/**
 * Tools → Seed — admin page to seed sample content.
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/../seeders/pages.php';
require_once __DIR__ . '/../seeders/posts.php';
require_once __DIR__ . '/../seeders/cases.php';
require_once __DIR__ . '/../seeders/services.php';
require_once __DIR__ . '/../seeders/partners.php';
require_once __DIR__ . '/../seeders/navigation.php';

add_action('admin_menu', function () {
    add_management_page(
        __('Seed', 'snel'),
        __('Seed', 'snel'),
        'manage_options',
        'snel-seed',
        'snel_seed_page_render'
    );
});

// ---------------------------------------------------------------------------
// Handle seed actions
// ---------------------------------------------------------------------------

add_action('admin_post_snel_seed_posts', function () {
    check_admin_referer('snel_seed_posts');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_posts();

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => $seeded,
        'type'   => 'posts',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_menu', function () {
    check_admin_referer('snel_seed_menu');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $result = snel_seed_menu();

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => (int) $result,
        'type'   => 'menu',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_services', function () {
    check_admin_referer('snel_seed_services');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_services();

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => $seeded,
        'type'   => 'services',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_services', function () {
    check_admin_referer('snel_reseed_services');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_services();

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => $seeded,
        'type'   => 'services',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_cases', function () {
    check_admin_referer('snel_seed_cases');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_cases();

    wp_redirect(add_query_arg([
        'page'    => 'snel-seed',
        'seeded'  => $seeded,
        'type'    => 'cases',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_posts', function () {
    check_admin_referer('snel_reseed_posts');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_posts(true);

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => $seeded,
        'type'   => 'posts',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_partners', function () {
    check_admin_referer('snel_seed_partners');
    if (! current_user_can('manage_options')) wp_die('Unauthorized');
    $seeded = snel_seed_partners(false);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => $seeded, 'type' => 'partners'], admin_url('admin.php')));
    exit;
});

add_action('admin_post_snel_reseed_partners', function () {
    check_admin_referer('snel_reseed_partners');
    if (! current_user_can('manage_options')) wp_die('Unauthorized');
    $seeded = snel_seed_partners(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => $seeded, 'type' => 'partners'], admin_url('admin.php')));
    exit;
});

add_action('admin_post_snel_reseed_cases', function () {
    check_admin_referer('snel_reseed_cases');

    if (! current_user_can('manage_options')) {
        wp_die(__('Geen toegang.', 'snel'));
    }

    $seeded = snel_seed_cases(true);

    wp_redirect(add_query_arg([
        'page'   => 'snel-seed',
        'seeded' => $seeded,
        'type'   => 'cases',
    ], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_blog_page', function () {
    check_admin_referer('snel_seed_blog_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_blog_page();
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'blog_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_blog_page', function () {
    check_admin_referer('snel_reseed_blog_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_blog_page(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'blog_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_cases_page', function () {
    check_admin_referer('snel_seed_cases_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_cases_page();
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'cases_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_cases_page', function () {
    check_admin_referer('snel_reseed_cases_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_cases_page(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'cases_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_websites_page', function () {
    check_admin_referer('snel_seed_websites_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_websites_page();
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'websites_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_websites_page', function () {
    check_admin_referer('snel_reseed_websites_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_websites_page(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'websites_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_front_page', function () {
    check_admin_referer('snel_seed_front_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_front_page();
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'front_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_front_page', function () {
    check_admin_referer('snel_reseed_front_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_front_page(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'front_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_seed_contact_page', function () {
    check_admin_referer('snel_seed_contact_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_contact_page();
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'contact_page'], admin_url('tools.php')));
    exit;
});

add_action('admin_post_snel_reseed_contact_page', function () {
    check_admin_referer('snel_reseed_contact_page');
    if (! current_user_can('manage_options')) wp_die(__('Geen toegang.', 'snel'));
    $ok = snel_seed_contact_page(true);
    wp_safe_redirect(add_query_arg(['page' => 'snel-seed', 'seeded' => (int) $ok, 'type' => 'contact_page'], admin_url('tools.php')));
    exit;
});

// ---------------------------------------------------------------------------
// Page render
// ---------------------------------------------------------------------------

function snel_seed_page_render(): void
{
    $case_count     = wp_count_posts('case')->publish ?? 0;
    $post_count     = wp_count_posts('post')->publish ?? 0;
    $service_count  = wp_count_posts('service')->publish ?? 0;
    $partner_count  = wp_count_posts('snel_partner')->publish ?? 0;

    $notice = '';
    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'menu') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Menu aangemaakt en toegewezen aan Primary.', 'snel') . '</p></div>'
            : '<div class="notice notice-error is-dismissible"><p>' . __('Menu aanmaken mislukt.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'services') {
        $n      = (int) $_GET['seeded'];
        $notice = $n > 0
            ? sprintf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', sprintf(__('%d dienst(en) aangemaakt.', 'snel'), $n))
            : '<div class="notice notice-info is-dismissible"><p>' . __('Geen nieuwe diensten aangemaakt — ze bestaan al.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'posts') {
        $n      = (int) $_GET['seeded'];
        $notice = $n > 0
            ? sprintf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', sprintf(__('%d blogpost(s) aangemaakt.', 'snel'), $n))
            : '<div class="notice notice-info is-dismissible"><p>' . __('Geen nieuwe posts aangemaakt — ze bestaan al.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'partners') {
        $n      = (int) $_GET['seeded'];
        $notice = $n > 0
            ? sprintf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', sprintf(__('%d partner(s) aangemaakt.', 'snel'), $n))
            : '<div class="notice notice-info is-dismissible"><p>' . __('Geen nieuwe partners aangemaakt — ze bestaan al.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'blog_page') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Blog-pagina geseed.', 'snel') . '</p></div>'
            : '<div class="notice notice-info is-dismissible"><p>' . __('Blog-pagina bestaat al — gebruik Re-seed om de content te overschrijven.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'cases_page') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Cases-pagina geseed.', 'snel') . '</p></div>'
            : '<div class="notice notice-info is-dismissible"><p>' . __('Cases-pagina bestaat al — gebruik Re-seed om de content te overschrijven.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'websites_page') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Websites-pagina geseed.', 'snel') . '</p></div>'
            : '<div class="notice notice-info is-dismissible"><p>' . __('Websites-pagina bestaat al — gebruik Re-seed om de content te overschrijven.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'front_page') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Homepagina geseed.', 'snel') . '</p></div>'
            : '<div class="notice notice-info is-dismissible"><p>' . __('Homepagina bestaat al — gebruik Re-seed om de content te overschrijven.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'contact_page') {
        $notice = (int) $_GET['seeded']
            ? '<div class="notice notice-success is-dismissible"><p>' . __('Contactpagina geseed.', 'snel') . '</p></div>'
            : '<div class="notice notice-info is-dismissible"><p>' . __('Contactpagina bestaat al — gebruik Re-seed om de content te overschrijven.', 'snel') . '</p></div>';
    }

    if (isset($_GET['seeded'], $_GET['type']) && $_GET['type'] === 'cases') {
        $n      = (int) $_GET['seeded'];
        $notice = $n > 0
            ? sprintf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', sprintf(__('%d case(s) aangemaakt.', 'snel'), $n))
            : '<div class="notice notice-info is-dismissible"><p>' . __('Geen nieuwe cases aangemaakt — ze bestaan al.', 'snel') . '</p></div>';
    }
    // Diagnostics.
    $primary_loc  = get_nav_menu_locations()['primary'] ?? 0;
    $primary_menu = $primary_loc ? wp_get_nav_menu_object($primary_loc) : null;
    $menu_items   = $primary_loc ? (wp_get_nav_menu_items($primary_loc) ?: []) : [];
    ?>
    <div class="wrap">
        <h1><?php _e('Seed', 'snel'); ?></h1>
        <?php echo $notice; ?>

        <details style="margin:16px 0;max-width:700px;border:1px solid #c3c4c7;border-radius:4px;padding:12px 16px;background:#f6f7f7">
            <summary style="cursor:pointer;font-weight:600">Diagnostics</summary>
            <p><strong>Primary menu:</strong> <?php echo $primary_menu ? esc_html($primary_menu->name) . ' (ID ' . $primary_menu->term_id . ')' : '<span style="color:red">Not assigned</span>'; ?></p>
            <p><strong>Service posts in DB:</strong> <?php echo (int) wp_count_posts('service')->publish; ?> published</p>
            <p><strong>Menu items (<?php echo count($menu_items); ?>):</strong></p>
            <ul>
                <?php foreach ($menu_items as $mi) : ?>
                    <li>[<?php echo $mi->ID; ?>] parent=<?php echo $mi->menu_item_parent ?: '—'; ?> · <em><?php echo esc_html($mi->title); ?></em> · type=<?php echo esc_html($mi->type); ?> / <?php echo esc_html($mi->object); ?></li>
                <?php endforeach; ?>
                <?php if (empty($menu_items)) echo '<li style="color:red">No menu items found</li>'; ?>
            </ul>
        </details>

        <div style="margin-top:24px;max-width:560px;display:flex;flex-direction:column;gap:16px">

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Navigatiemenu', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php _e('Maakt "Hoofdmenu" aan (of reset het) en wijst het toe aan de Primary locatie. Items: Diensten (met submenu per dienst), Cases, Blog.', 'snel'); ?>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_menu'); ?>
                    <input type="hidden" name="action" value="snel_seed_menu" />
                    <button type="submit" class="button button-primary"><?php _e('Seed menu', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Menu wordt gereset en opnieuw aangemaakt. Doorgaan?')">
                    <?php wp_nonce_field('snel_seed_menu'); ?>
                    <input type="hidden" name="action" value="snel_seed_menu" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (wis + hermaak)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Diensten', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php printf(__('Huidig aantal gepubliceerde diensten: <strong>%d</strong>', 'snel'), $service_count); ?>
                </p>
                <p style="color:#646970">
                    <?php _e('Maakt 4 voorbeelddiensten aan. Al bestaande diensten worden overgeslagen.', 'snel'); ?>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_services'); ?>
                    <input type="hidden" name="action" value="snel_seed_services" />
                    <button type="submit" class="button button-primary"><?php _e('Seed diensten', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Alle bestaande diensten worden verwijderd. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_services'); ?>
                    <input type="hidden" name="action" value="snel_reseed_services" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (wis + hermaak)', 'snel'); ?></button>
                </form>
            </div>
            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Blogposts', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php printf(__('Huidig aantal gepubliceerde posts: <strong>%d</strong>', 'snel'), $post_count); ?>
                </p>
                <p style="color:#646970">
                    <?php _e('Maakt 10 voorbeeldblogposts aan over AI, WordPress en automatisering voor het MKB. Al bestaande posts worden overgeslagen.', 'snel'); ?>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_posts'); ?>
                    <input type="hidden" name="action" value="snel_seed_posts" />
                    <button type="submit" class="button button-primary"><?php _e('Seed blogposts', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Alle bestaande posts worden verwijderd. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_posts'); ?>
                    <input type="hidden" name="action" value="snel_reseed_posts" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (wis + hermaak)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Blog pagina', 'snel'); ?></h2>
                <p style="color:#646970"><?php _e('Maakt de /blog/ pagina aan (intro + posts archive). Re-seed overschrijft alleen de content.', 'snel'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_blog_page'); ?>
                    <input type="hidden" name="action" value="snel_seed_blog_page" />
                    <button type="submit" class="button button-primary"><?php _e('Seed blog pagina', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Content van de blog-pagina wordt overschreven. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_blog_page'); ?>
                    <input type="hidden" name="action" value="snel_reseed_blog_page" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (overschrijf content)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Cases', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php printf(__('Huidig aantal gepubliceerde cases: <strong>%d</strong>', 'snel'), $case_count); ?>
                </p>
                <p style="color:#646970">
                    <?php _e('Maakt voorbeeldcases aan. Al bestaande cases worden overgeslagen.', 'snel'); ?>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_cases'); ?>
                    <input type="hidden" name="action" value="snel_seed_cases" />
                    <button type="submit" class="button button-primary"><?php _e('Seed cases', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Alle bestaande cases worden verwijderd. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_cases'); ?>
                    <input type="hidden" name="action" value="snel_reseed_cases" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (wis + hermaak)', 'snel'); ?></button>
                </form>
            </div>
            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Cases pagina', 'snel'); ?></h2>
                <p style="color:#646970"><?php _e('Maakt de /cases/ pagina aan (intro + cases grid + content). Navigatiemenu wijst al naar /cases/.', 'snel'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_cases_page'); ?>
                    <input type="hidden" name="action" value="snel_seed_cases_page" />
                    <button type="submit" class="button button-primary"><?php _e('Seed cases pagina', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Content van de cases-pagina wordt overschreven. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_cases_page'); ?>
                    <input type="hidden" name="action" value="snel_reseed_cases_page" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (overschrijf content)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Websites pagina', 'snel'); ?></h2>
                <p style="color:#646970"><?php _e('Maakt de /websites/ pagina aan met Snelstack copy (design + 10jr ervaring + AI + solo-voordeel). Re-seed overschrijft alleen de content.', 'snel'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_websites_page'); ?>
                    <input type="hidden" name="action" value="snel_seed_websites_page" />
                    <button type="submit" class="button button-primary"><?php _e('Seed websites pagina', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Content van de websites-pagina wordt overschreven. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_websites_page'); ?>
                    <input type="hidden" name="action" value="snel_reseed_websites_page" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (overschrijf content)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Homepagina', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php
                    $fp_id = (int) get_option('page_on_front');
                    printf(__('Huidige front page: %s', 'snel'), $fp_id ? '<strong>' . esc_html(get_the_title($fp_id)) . '</strong> (ID ' . $fp_id . ')' : '<span style="color:red">Niet ingesteld</span>');
                    ?>
                </p>
                <p style="color:#646970"><?php _e('Maakt de Home-pagina aan met Snelstack-copy en stelt deze in als statische voorpagina. Re-seed overschrijft alleen de content, de pagina zelf blijft bestaan.', 'snel'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_front_page'); ?>
                    <input type="hidden" name="action" value="snel_seed_front_page" />
                    <button type="submit" class="button button-primary"><?php _e('Seed homepagina', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Content van de homepagina wordt overschreven. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_front_page'); ?>
                    <input type="hidden" name="action" value="snel_reseed_front_page" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (overschrijf content)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Partners', 'snel'); ?></h2>
                <p style="color:#646970">
                    <?php printf(__('Huidig aantal gepubliceerde partners: <strong>%d</strong>', 'snel'), $partner_count); ?>
                </p>
                <p style="color:#646970">
                    <?php _e('Maakt 5 voorbeeldpartners aan met logo uit assets/images/partners/.', 'snel'); ?>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_partners'); ?>
                    <input type="hidden" name="action" value="snel_seed_partners" />
                    <button type="submit" class="button button-primary"><?php _e('Seed partners', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Alle bestaande partners worden verwijderd. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_partners'); ?>
                    <input type="hidden" name="action" value="snel_reseed_partners" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (wis + hermaak)', 'snel'); ?></button>
                </form>
            </div>

            <div style="border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;background:#fff">
                <h2 style="margin-top:0"><?php _e('Contactpagina', 'snel'); ?></h2>
                <p style="color:#646970"><?php _e('Maakt de /contact/ pagina aan (hero + contactformulier). Re-seed overschrijft alleen de content.', 'snel'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:8px">
                    <?php wp_nonce_field('snel_seed_contact_page'); ?>
                    <input type="hidden" name="action" value="snel_seed_contact_page" />
                    <button type="submit" class="button button-primary"><?php _e('Seed contactpagina', 'snel'); ?></button>
                </form>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block" onsubmit="return confirm('Content van de contactpagina wordt overschreven. Doorgaan?')">
                    <?php wp_nonce_field('snel_reseed_contact_page'); ?>
                    <input type="hidden" name="action" value="snel_reseed_contact_page" />
                    <button type="submit" class="button button-secondary"><?php _e('Re-seed (overschrijf content)', 'snel'); ?></button>
                </form>
            </div>

        </div>
    </div>
    <?php
}
