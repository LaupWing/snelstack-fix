<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class('antialiased'); ?>>
    <?php wp_body_open(); ?>

    <?php
    $langs   = snel_get_supported_langs();
    $current = snel_get_lang();
    $config  = snel_get_languages_config();

    $lang_full_names = [
        'nl' => 'Nederlands',
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'es' => 'Español',
        'it' => 'Italiano',
    ];
    $lang_flags = [
        'nl' => '🇳🇱',
        'en' => '🇬🇧',
        'de' => '🇩🇪',
        'fr' => '🇫🇷',
        'es' => '🇪🇸',
        'it' => '🇮🇹',
    ];

    // Get primary menu items.
    $menu_locations = get_nav_menu_locations();
    $menu_items     = [];
    if (! empty($menu_locations['primary'])) {
        $menu_items = wp_get_nav_menu_items($menu_locations['primary']) ?: [];
    }

    // Build menu tree (top-level items + their children).
    $menu_tree = [];
    foreach ($menu_items as $item) {
        if ((int) $item->menu_item_parent === 0) {
            $item->children = [];
            $menu_tree[] = $item;
        }
    }
    foreach ($menu_items as $child) {
        if ((int) $child->menu_item_parent === 0) continue;
        foreach ($menu_tree as &$top) {
            if ((int) $top->ID === (int) $child->menu_item_parent) {
                $top->children[] = $child;
                break;
            }
        }
        unset($top);
    }

    // Current path for active-link detection.
    $snel_current_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $snel_current_path = '/' . trim($snel_current_path, '/');

    // Business assets.
    $site_name = get_bloginfo('name');
    $email     = snel_business('email');

    // Social links.
    $socials = array_filter([
        'x'        => snel_business('x_url'),
        'linkedin' => snel_business('linkedin_url'),
        'youtube'  => snel_business('youtube_url'),
    ]);
    $social_paths = [
        'x'        => 'M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z',
        'linkedin' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
        'youtube'  => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
    ];

    $nav_active_classes   = 'animate-gradient-x bg-gradient-to-r from-blue-500 via-violet-500 to-blue-500 bg-[length:200%_100%] bg-clip-text font-semibold text-transparent';
    $nav_inactive_classes = 'font-medium text-slate-700 hover:text-slate-900';

    $contact_href = $email
        ? 'mailto:' . antispambot($email) . '?subject=' . rawurlencode('Project Aanvraag')
        : '#';

    $chevron_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3.5 transition-transform duration-200"><path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>';
    $arrow_svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 transition-transform duration-200 group-hover:-rotate-45"><path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd" /></svg>';
    ?>

    <div class="fixed inset-x-0 top-0 z-50 px-4 pt-4 md:px-8">
        <!-- Breathing gradient glow behind header -->
        <div class="pointer-events-none absolute left-1/2 top-0 h-24 w-[600px] -translate-x-1/2 animate-header-glow rounded-full bg-gradient-to-r from-violet-300/30 via-violet-400/40 to-violet-500/30 blur-3xl"></div>

        <div id="snel-header" class="relative mx-auto max-w-5xl">
            <header class="relative flex items-center justify-between rounded-full border border-gray-200 bg-white px-4 py-2.5 shadow-lg md:px-6">
                <div class="flex items-center gap-3 md:gap-6">
                    <!-- Mobile menu toggle -->
                    <button
                        type="button"
                        id="snel-mobile-toggle"
                        class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-gray-100 transition-colors hover:bg-gray-200 md:hidden"
                        aria-label="<?php echo esc_attr(snel__('Toggle menu')); ?>"
                        aria-expanded="false">
                        <svg class="snel-icon-menu h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6" />
                            <line x1="4" y1="12" x2="20" y2="12" />
                            <line x1="4" y1="18" x2="20" y2="18" />
                        </svg>
                        <svg class="snel-icon-close hidden h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="<?php echo esc_url(snel_url('/')); ?>" class="flex items-center" aria-label="<?php echo esc_attr($site_name); ?>">
                        <span class="md:hidden">
                            <?php get_template_part('template-parts/logo', null, ['hide_text' => true]); ?>
                        </span>
                        <span class="hidden md:inline-flex">
                            <?php get_template_part('template-parts/logo'); ?>
                        </span>
                    </a>

                    <!-- Desktop nav -->
                    <nav id="snel-nav" class="relative hidden items-center gap-1 md:flex">
                        <?php foreach ($menu_tree as $item) :
                            $resolved     = snel_nav_item($item);
                            $url          = $resolved['url'];
                            $title        = $resolved['title'];
                            $item_path    = '/' . trim((string) parse_url($url, PHP_URL_PATH), '/');
                            $has_children = ! empty($item->children);
                            $is_active    = ($item_path === $snel_current_path)
                                         || ($item_path === '/cases' && is_singular('case'));
                            if (! $is_active && $has_children) {
                                foreach ($item->children as $child) {
                                    $ch_url = snel_nav_item($child)['url'];
                                    if ('/' . trim((string) parse_url($ch_url, PHP_URL_PATH), '/') === $snel_current_path) {
                                        $is_active = true;
                                        break;
                                    }
                                }
                            }
                        ?>
                            <?php if ($has_children) : ?>
                                <a href="<?php echo esc_url($url); ?>"
                                    data-dropdown-trigger="item-<?php echo esc_attr($item->ID); ?>"
                                    class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-1.5 text-sm transition-all <?php echo $is_active ? $nav_active_classes : $nav_inactive_classes; ?>"
                                    <?php if ($is_active) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html($title); ?>
                                    <span id="snel-chevron-item-<?php echo esc_attr($item->ID); ?>" class="mt-px text-slate-400 transition-transform duration-200">
                                        <?php echo $chevron_svg; ?>
                                    </span>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url($url); ?>"
                                    class="rounded-full px-3 py-1.5 text-sm transition-all <?php echo $is_active ? $nav_active_classes : $nav_inactive_classes; ?>"
                                    <?php if ($is_active) echo 'aria-current="page"'; ?>>
                                    <?php echo esc_html($title); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($menu_tree)) : ?>
                            <span class="text-sm text-gray-400"><?php echo esc_html(snel__('Set up a menu in Appearance > Menus')); ?></span>
                        <?php endif; ?>
                    </nav>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden items-center gap-3 md:flex">
                        <?php foreach ($socials as $key => $link) : ?>
                            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"
                                aria-label="<?php echo esc_attr(ucfirst($key)); ?>"
                                class="text-gray-500 transition-colors hover:text-gray-900">
                                <svg class="h-4 w-4" role="img" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="<?php echo esc_attr($social_paths[$key]); ?>" />
                                </svg>
                            </a>
                        <?php endforeach; ?>

                        <!-- Language Switcher -->
                        <div class="relative" id="snel-lang-switcher">
                            <button
                                type="button"
                                id="snel-lang-btn"
                                class="flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 cursor-pointer"
                                aria-expanded="false"
                                aria-haspopup="true">
                                <span class="text-base leading-none"><?php echo $lang_flags[$current] ?? '🌐'; ?></span>
                                <span><?php echo esc_html($config[$current]['label'] ?? strtoupper($current)); ?></span>
                                <svg class="h-4 w-4 opacity-50 transition-transform" id="snel-lang-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                id="snel-lang-popover"
                                class="invisible absolute right-0 top-full z-50 mt-2 w-56 origin-top-right scale-95 overflow-hidden rounded-2xl bg-white opacity-0 shadow-[0px_4px_8px_rgba(34,42,53,0.05),0px_0px_0px_1px_rgba(34,42,53,0.04),0px_1px_5px_-4px_rgba(19,19,22,0.7)] transition-all duration-200 ease-out"
                                role="menu">
                                <div class="py-1" id="snel-lang-list">
                                    <?php foreach ($langs as $lang) :
                                        $lurl      = snel_lang_url($lang);
                                        $label     = $config[$lang]['label'] ?? strtoupper($lang);
                                        $full_name = $lang_full_names[$lang] ?? $label;
                                        $flag      = $lang_flags[$lang] ?? '🌐';
                                        $is_active = ($lang === $current);
                                    ?>
                                        <a
                                            href="<?php echo esc_url($lurl); ?>"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm transition-colors <?php echo $is_active ? 'bg-gray-50 font-medium text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>"
                                            role="menuitem"
                                            <?php if ($is_active) echo 'aria-current="true"'; ?>>
                                            <span class="text-lg leading-none"><?php echo $flag; ?></span>
                                            <span class="flex-1"><?php echo esc_html($full_name); ?></span>
                                            <span class="font-mono text-xs text-gray-400"><?php echo esc_html($label); ?></span>
                                            <?php if ($is_active) : ?>
                                                <svg class="h-4 w-4 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gradient CTA button -->
                    <?php
                    get_template_part('template-parts/gradient-button', null, [
                        'href'       => $contact_href,
                        'label'      => snel__('Contact'),
                        'icon'       => '<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
                        'face_class' => 'px-4 py-2 text-sm md:px-5',
                    ]);
                    ?>
                </div>
            </header>

            <?php /* Mega dropdown panels — one per top-level item with children */ ?>
            <?php foreach ($menu_tree as $item) :
                if (empty($item->children)) continue;
                $resolved     = snel_nav_item($item);
                $archive_url  = get_post_type_archive_link($item->object) ?: $resolved['url'];
            ?>
            <div id="snel-dropdown-item-<?php echo esc_attr($item->ID); ?>"
                 class="pointer-events-none invisible absolute left-0 right-0 top-full z-40 mt-2 origin-top scale-95 opacity-0 transition-all duration-200 ease-out">
                <div class="overflow-hidden rounded-2xl bg-white/90 backdrop-blur-xl shadow-[0px_4px_8px_rgba(34,42,53,0.05),0px_0px_0px_1px_rgba(34,42,53,0.04),0px_1px_5px_-4px_rgba(19,19,22,0.7)]">
                    <div class="grid grid-cols-2">
                        <?php foreach ($item->children as $child) :
                            $ch      = snel_nav_item($child);
                            $icon    = get_post_meta($child->object_id, '_service_icon', true);
                            $excerpt = get_the_excerpt($child->object_id);
                        ?>
                        <a href="<?php echo esc_url($ch['url']); ?>" class="group relative flex border-b border-slate-100 p-6">
                            <div class="relative z-10 flex flex-col gap-1.5">
                                <span class="flex items-center gap-2">
                                    <?php if ($icon) : ?>
                                        <span class="shrink-0 text-xl leading-none"><?php echo esc_html($icon); ?></span>
                                    <?php endif; ?>
                                    <span class="font-medium text-slate-900"><?php echo esc_html($ch['title']); ?></span>
                                </span>
                                <?php if ($excerpt) : ?>
                                    <p class="grow text-sm text-slate-500 transition group-hover:text-slate-800"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                                <span class="mt-1 flex items-center gap-1.5 text-sm font-medium text-brand-primary">
                                    <?php echo esc_html(snel__('Meer info')); ?>
                                    <?php echo $arrow_svg; ?>
                                </span>
                            </div>
                            <div class="absolute inset-2 z-0 scale-90 rounded-lg bg-brand-primary/10 opacity-0 transition-all duration-200 group-hover:scale-100 group-hover:opacity-100"></div>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <a href="<?php echo esc_url($archive_url); ?>"
                       class="group flex w-full items-center justify-center gap-2 p-4 text-sm font-medium text-slate-600 transition hover:bg-brand-primary/5 hover:text-brand-primary">
                        <?php printf(esc_html(snel__('Bekijk alle %s')), esc_html(strtolower($resolved['title']))); ?>
                        <?php echo $arrow_svg; ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Mobile menu dropdown -->
            <div
                id="snel-mobile-menu"
                class="invisible absolute left-0 right-0 top-full mt-2 max-h-0 overflow-hidden rounded-2xl border border-transparent bg-white/80 opacity-0 shadow-lg backdrop-blur-xl transition-all duration-300 ease-out md:hidden">
                <nav class="flex flex-col p-4">
                    <?php foreach ($menu_tree as $item) :
                        $resolved  = snel_nav_item($item);
                        $url       = $resolved['url'];
                        $title     = $resolved['title'];
                        $item_path = '/' . trim((string) parse_url($url, PHP_URL_PATH), '/');
                        $is_active = ($item_path === $snel_current_path);
                    ?>
                        <a href="<?php echo esc_url($url); ?>"
                            class="rounded-lg px-4 py-3 text-sm transition-colors <?php echo $is_active ? 'bg-gradient-to-r from-blue-500/10 to-violet-500/10' : 'hover:bg-gray-100'; ?>">
                            <span class="<?php echo $is_active ? $nav_active_classes : 'font-medium text-gray-800'; ?>">
                                <?php echo esc_html($title); ?>
                            </span>
                        </a>
                        <?php foreach ($item->children as $child) :
                            $ch   = snel_nav_item($child);
                            $icon = get_post_meta($child->object_id, '_service_icon', true);
                        ?>
                        <a href="<?php echo esc_url($ch['url']); ?>"
                            class="flex items-center gap-2 rounded-lg px-6 py-2 text-sm text-slate-500 transition-colors hover:bg-gray-50 hover:text-slate-900">
                            <?php if ($icon) : ?>
                                <span class="leading-none"><?php echo esc_html($icon); ?></span>
                            <?php endif; ?>
                            <?php echo esc_html($ch['title']); ?>
                        </a>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                    <div class="my-3 h-px bg-gray-200"></div>

                    <!-- Mobile language list -->
                    <div class="flex flex-wrap gap-2 px-4 py-2">
                        <?php foreach ($langs as $lang) :
                            $lurl      = snel_lang_url($lang);
                            $label     = $config[$lang]['label'] ?? strtoupper($lang);
                            $flag      = $lang_flags[$lang] ?? '🌐';
                            $is_active = ($lang === $current);
                        ?>
                            <a href="<?php echo esc_url($lurl); ?>"
                                class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm transition-colors <?php echo $is_active ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <span class="leading-none"><?php echo $flag; ?></span>
                                <span><?php echo esc_html($label); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <?php if (! empty($socials)) : ?>
                        <div class="flex items-center gap-4 px-4 py-2">
                            <?php foreach ($socials as $key => $link) : ?>
                                <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"
                                    aria-label="<?php echo esc_attr(ucfirst($key)); ?>"
                                    class="text-gray-500 transition-colors hover:text-gray-900">
                                    <svg class="h-5 w-5" role="img" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="<?php echo esc_attr($social_paths[$key]); ?>" />
                                    </svg>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>

            <script>
            (function () {
                // ── Flying pill hover effect ─────────────────────────────────
                var nav = document.getElementById('snel-nav');
                if (nav) {
                    var pill = document.createElement('span');
                    pill.setAttribute('aria-hidden', 'true');
                    pill.style.cssText = 'position:absolute;border-radius:9999px;background:rgb(243 244 246);pointer-events:none;opacity:0;transition:left .18s ease,top .18s ease,width .18s ease,height .18s ease,opacity .12s ease;z-index:0;';
                    nav.insertBefore(pill, nav.firstChild);

                    var pillTimer;
                    nav.querySelectorAll('a, button').forEach(function (item) {
                        item.style.position = 'relative';
                        item.style.zIndex   = '1';
                        item.addEventListener('mouseenter', function () {
                            clearTimeout(pillTimer);
                            var nr = nav.getBoundingClientRect();
                            var ir = item.getBoundingClientRect();
                            pill.style.left    = (ir.left - nr.left) + 'px';
                            pill.style.top     = (ir.top  - nr.top)  + 'px';
                            pill.style.width   = ir.width  + 'px';
                            pill.style.height  = ir.height + 'px';
                            pill.style.opacity = '1';
                        });
                    });
                    nav.addEventListener('mouseleave', function () {
                        pillTimer = setTimeout(function () { pill.style.opacity = '0'; }, 120);
                    });
                }

                // ── Flying pill in dropdown grids ───────────────────────────
                document.querySelectorAll('[id^="snel-dropdown-"] .grid').forEach(function (grid) {
                    grid.style.position = 'relative';

                    var dpill = document.createElement('span');
                    dpill.setAttribute('aria-hidden', 'true');
                    dpill.style.cssText = 'position:absolute;border-radius:0.5rem;background:rgb(139 92 246/.08);pointer-events:none;opacity:0;transition:left .18s ease,top .18s ease,width .18s ease,height .18s ease,opacity .12s ease;z-index:0;';
                    grid.insertBefore(dpill, grid.firstChild);

                    // hide the per-card static fill divs — pill replaces them
                    grid.querySelectorAll('a > div.absolute').forEach(function (fill) {
                        fill.style.display = 'none';
                    });

                    var dpillTimer;
                    grid.querySelectorAll('a').forEach(function (card) {
                        card.style.position = 'relative';
                        card.style.zIndex   = '1';
                        card.addEventListener('mouseenter', function () {
                            clearTimeout(dpillTimer);
                            var gr = grid.getBoundingClientRect();
                            var cr = card.getBoundingClientRect();
                            dpill.style.left    = (cr.left - gr.left) + 'px';
                            dpill.style.top     = (cr.top  - gr.top)  + 'px';
                            dpill.style.width   = cr.width  + 'px';
                            dpill.style.height  = cr.height + 'px';
                            dpill.style.opacity = '1';
                        });
                    });
                    grid.addEventListener('mouseleave', function () {
                        dpillTimer = setTimeout(function () { dpill.style.opacity = '0'; }, 120);
                    });
                });

                // ── Mega dropdown ────────────────────────────────────────────
                var triggers = document.querySelectorAll('[data-dropdown-trigger]');
                triggers.forEach(function (trigger) {
                    var key     = trigger.dataset.dropdownTrigger;
                    var panel   = document.getElementById('snel-dropdown-' + key);
                    var chevron = document.getElementById('snel-chevron-' + key);
                    if (!panel) return;
                    var timer;

                    function show() {
                        clearTimeout(timer);
                        panel.classList.remove('invisible', 'opacity-0', 'scale-95', 'pointer-events-none');
                        panel.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    }
                    function hide() {
                        timer = setTimeout(function () {
                            panel.classList.add('invisible', 'opacity-0', 'scale-95', 'pointer-events-none');
                            panel.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                            if (chevron) chevron.style.transform = '';
                        }, 150);
                    }

                    trigger.addEventListener('mouseenter', show);
                    trigger.addEventListener('mouseleave', hide);
                    panel.addEventListener('mouseenter', function () { clearTimeout(timer); });
                    panel.addEventListener('mouseleave', hide);
                });
            })();
            </script>
        </div>
    </div>

    <main class="rounded-b-2xl overflow-hidden">
