<?php
/**
 * Snelstack Settings — Entry Point.
 *
 * Provides shared configuration for all Snelstack plugins.
 * API keys and AI model settings live here.
 *
 * @package AntiqueWarehouse
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Source of truth for branded admin menu icons.
 * Key = menu slug (4th arg of add_menu_page). Value = inner SVG markup.
 * Filter `snel_admin_icons` lets plugins register their own.
 */
function snelstack_get_admin_icons() {
    return apply_filters( 'snel_admin_icons', array(
        'snel-seo'          => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>',
        'snel-translations' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2v3"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>',
        'snel-newsletter'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
        'snelstack'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" fill="#fff"/></svg>',
    ) );
}

/**
 * Prevent WordPress from overriding SVG icon colors on Snel menu items.
 */
add_action( 'admin_head', function () {
    $slugs    = array_keys( snelstack_get_admin_icons() );
    $sel_img  = implode( ',', array_map( function ( $s ) { return "#adminmenu .toplevel_page_{$s} .wp-menu-image"; }, $slugs ) );
    $sel_li   = implode( ',', array_map( function ( $s ) { return "#adminmenu .toplevel_page_{$s}"; }, $slugs ) );
    $sel_br   = implode( ',', array_map( function ( $s ) { return "#adminmenu .toplevel_page_{$s} .wp-menu-image br"; }, $slugs ) );
    ?>
    <style>
        @keyframes snel-gradient-spin {
            0%   { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .snel-menu-icon {
            display: inline-block;
            width: 22px;
            height: 22px;
            background: linear-gradient(135deg, #3b82f6, #7c3aed);
            border-radius: 50%;
            position: relative;
            vertical-align: middle;
            overflow: hidden;
        }
        .snel-menu-icon svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 14px;
            height: 14px;
            z-index: 2;
        }
        .snel-menu-icon.is-active {
            background: none;
        }
        .snel-gradient-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 32px;
            height: 32px;
            background: conic-gradient(from 0deg, #06b6d4, #3b82f6, #8b5cf6, #d946ef, #f43f5e, #f97316, #eab308, #22c55e, #06b6d4);
            animation: snel-gradient-spin 3s linear infinite;
            z-index: 1;
        }
        <?php echo $sel_img; ?> {
            display: flex !important;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: none !important;
        }
        <?php echo $sel_li; ?> {
            position: relative;
            z-index: 1;
        }
        <?php echo $sel_br; ?> {
            display: none;
        }
    </style>
    <?php
} );

/**
 * Replace SVG icon markup with custom branded icon for all Snel menu items.
 */
add_action( 'admin_footer', function () {
    $icons     = snelstack_get_admin_icons();
    $slugs     = array_keys( $icons );
    $selector  = implode( ',', array_map( function ( $s ) { return "#adminmenu .toplevel_page_{$s} .wp-menu-image"; }, $slugs ) );
    ?>
    <script>
        (function () {
            var snelIcons = <?php echo wp_json_encode( $icons ); ?>;
            document.querySelectorAll(<?php echo wp_json_encode( $selector ); ?>).forEach(function (el) {
                var li = el.closest('li');
                var isActive = li && (li.classList.contains('wp-has-current-submenu') || li.classList.contains('current'));
                var ring = isActive ? '<span class="snel-gradient-ring"></span>' : '';
                var activeClass = isActive ? ' is-active' : '';
                var slug = li.className.match(/toplevel_page_([\w-]+)/);
                var svg = slug ? (snelIcons[slug[1]] || snelIcons['snelstack']) : snelIcons['snelstack'];
                el.innerHTML = '<span class="snel-menu-icon' + activeClass + '">' + ring + svg + '</span>';
            });
        })();
    </script>
    <?php
} );

/**
 * Register the Snelstack settings page.
 */
add_action( 'admin_menu', function () {
    add_menu_page(
        __( 'Snel Stack', 'snel' ),
        __( 'Snel Stack', 'snel' ),
        'manage_options',
        'snelstack',
        'snelstack_render_settings',
        'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDI0IDI0Ij48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjM2I4MmY2Ii8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjN2MzYWVkIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTIiIGZpbGw9InVybCgjZykiLz48cGF0aCBkPSJNNi41IDEzYS43LjcgMCAwIDEtLjU1LTEuMTRsNi45My03LjE0YS4zNS4zNSAwIDAgMSAuNi4zMkwxMi4xNCA5LjJhLjcuNyAwIDAgMCAuNjYuOTVoNC45YS43LjcgMCAwIDEgLjU1IDEuMTRsLTYuOTMgNy4xNGEuMzUuMzUgMCAwIDEtLjYtLjMybDEuMzQtNC4yMUEuNy43IDAgMCAwIDExLjQgMTN6IiBmaWxsPSIjZmZmIi8+PC9zdmc+',
        29
    );
} );

// AI features run on the native WordPress AI Client (WP 7.0+). The provider
// and API key are managed under Settings → Connectors — there is no key
// handling in the theme.

/**
 * Render the Snelstack landing page.
 *
 * Reports whether an AI provider is configured via the native AI Client.
 */
function snelstack_render_settings() {
    $supported = function_exists( 'wp_ai_client_prompt' )
        && wp_ai_client_prompt( 'test' )->is_supported_for_text_generation();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Snel Stack', 'snel' ); ?></h1>
        <p style="color: #666; margin-bottom: 24px;">
            <?php esc_html_e( 'Shared configuration for all Snelstack plugins (Snel SEO, Translations, etc.).', 'snel' ); ?>
        </p>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'AI features', 'snel' ); ?></th>
                <td>
                    <?php if ( $supported ) : ?>
                        <p style="color: #059669; font-weight: 600; margin: 0;">
                            &#10003; <?php esc_html_e( 'AI provider configured — AI features are active.', 'snel' ); ?>
                        </p>
                    <?php else : ?>
                        <p style="color: #d63638; font-weight: 600; margin: 0;">
                            &#10007; <?php esc_html_e( 'No AI provider — AI features are disabled (manual translation still works).', 'snel' ); ?>
                        </p>
                    <?php endif; ?>
                    <p class="description" style="margin-top: 12px; max-width: 640px;">
                        <?php esc_html_e( 'AI generation and auto-translate use the native WordPress AI Client (WP 7.0+). Configure a provider (OpenAI, Anthropic, Google…) under Settings → Connectors.', 'snel' ); ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
