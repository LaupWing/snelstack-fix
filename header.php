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
$langs   = function_exists('snel_get_supported_langs') ? snel_get_supported_langs() : ['nl'];
$current = function_exists('snel_get_lang') ? snel_get_lang() : 'nl';
$config  = function_exists('snel_get_languages_config') ? snel_get_languages_config() : [];

$lang_full_names = ['nl' => 'Nederlands', 'en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français'];
$lang_flags      = ['nl' => '🇳🇱', 'en' => '🇬🇧', 'de' => '🇩🇪', 'fr' => '🇫🇷'];

$site_name = get_bloginfo('name');
$email     = get_option('admin_email');

$menu_locations = get_nav_menu_locations();
$menu_items     = [];
if (! empty($menu_locations['primary'])) {
    $menu_items = wp_get_nav_menu_items($menu_locations['primary']) ?: [];
}

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

$current_path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$contact_href = $email ? 'mailto:' . antispambot($email) : '#';

$nav_active_classes   = 'animate-gradient-x bg-gradient-to-r from-blue-500 via-violet-500 to-blue-500 bg-[length:200%_100%] bg-clip-text font-semibold text-transparent';
$nav_inactive_classes = 'font-medium text-slate-700 hover:text-slate-900';

$chevron_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3.5 transition-transform duration-200"><path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>';
?>

<div class="fixed inset-x-0 top-0 z-50 px-4 pt-4 md:px-8">
    <div class="pointer-events-none absolute left-1/2 top-0 h-24 w-[600px] -translate-x-1/2 animate-header-glow rounded-full bg-gradient-to-r from-violet-300/30 via-violet-400/40 to-violet-500/30 blur-3xl"></div>

    <div id="snel-header" class="relative mx-auto max-w-5xl">
        <header class="relative flex items-center justify-between rounded-full border border-white/30 bg-white/90 px-4 py-2.5 shadow-lg backdrop-blur-xl md:px-6">
            <div class="flex items-center gap-3 md:gap-6">

                <!-- Mobile toggle -->
                <button type="button" id="snel-mobile-toggle"
                    class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-gray-100 transition-colors hover:bg-gray-200 md:hidden"
                    aria-label="Toggle menu" aria-expanded="false">
                    <svg class="snel-icon-menu h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="18" x2="20" y2="18" />
                    </svg>
                    <svg class="snel-icon-close hidden h-5 w-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center" aria-label="<?php echo esc_attr($site_name); ?>">
                    <span class="md:hidden"><?php get_template_part('template-parts/logo', null, ['hide_text' => true]); ?></span>
                    <span class="hidden md:inline-flex"><?php get_template_part('template-parts/logo'); ?></span>
                </a>

                <!-- Desktop nav -->
                <nav id="snel-nav" class="relative hidden items-center gap-1 md:flex">
                    <?php foreach ($menu_tree as $item) :
                        $url          = $item->url;
                        $title        = $item->title;
                        $item_path    = '/' . trim(parse_url($url, PHP_URL_PATH), '/');
                        $is_active    = ($item_path === $current_path);
                        $has_children = ! empty($item->children);
                    ?>
                        <?php if ($has_children) : ?>
                            <a href="<?php echo esc_url($url); ?>"
                                data-dropdown-trigger="item-<?php echo esc_attr($item->ID); ?>"
                                class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-1.5 text-sm transition-all <?php echo $is_active ? $nav_active_classes : $nav_inactive_classes; ?>">
                                <?php echo esc_html($title); ?>
                                <span id="snel-chevron-item-<?php echo esc_attr($item->ID); ?>" class="mt-px text-slate-400"><?php echo $chevron_svg; ?></span>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url($url); ?>"
                                class="rounded-full px-3 py-1.5 text-sm transition-all <?php echo $is_active ? $nav_active_classes : $nav_inactive_classes; ?>">
                                <?php echo esc_html($title); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Language + CTA -->
            <div class="flex items-center gap-3">

                <!-- Language switcher -->
                <div class="relative" id="snel-lang-switcher">
                    <button type="button" id="snel-lang-btn"
                        class="flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 cursor-pointer"
                        aria-expanded="false" aria-haspopup="true">
                        <span class="text-base leading-none"><?php echo $lang_flags[$current] ?? '🌐'; ?></span>
                        <span><?php echo esc_html($config[$current]['label'] ?? strtoupper($current)); ?></span>
                        <svg class="h-4 w-4 opacity-50 transition-transform" id="snel-lang-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="snel-lang-popover"
                        class="invisible absolute right-0 top-full z-50 mt-2 w-56 origin-top-right scale-95 overflow-hidden rounded-2xl bg-white/90 opacity-0 backdrop-blur-xl shadow-[0px_4px_8px_rgba(34,42,53,0.05),0px_0px_0px_1px_rgba(34,42,53,0.04)] transition-all duration-200 ease-out"
                        role="menu">
                        <div class="py-1">
                            <?php foreach ($langs as $lang) :
                                $lurl      = function_exists('snel_lang_url') ? snel_lang_url($lang) : "/{$lang}/";
                                $label     = $config[$lang]['label'] ?? strtoupper($lang);
                                $full_name = $lang_full_names[$lang] ?? $label;
                                $flag      = $lang_flags[$lang] ?? '🌐';
                                $is_active = ($lang === $current);
                            ?>
                                <a href="<?php echo esc_url($lurl); ?>"
                                    class="flex items-center gap-3 px-3 py-2.5 text-sm transition-colors <?php echo $is_active ? 'bg-gray-50 font-medium text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?>"
                                    role="menuitem" <?php if ($is_active) echo 'aria-current="true"'; ?>>
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

                <?php get_template_part('template-parts/gradient-button', null, [
                    'href'       => $contact_href,
                    'label'      => 'Contact',
                    'face_class' => 'px-4 py-2 text-sm md:px-5',
                ]); ?>
            </div>
        </header>

        <!-- Mega dropdowns -->
        <?php foreach ($menu_tree as $item) :
            if (empty($item->children)) continue;
        ?>
        <div id="snel-dropdown-item-<?php echo esc_attr($item->ID); ?>"
             class="pointer-events-none invisible absolute left-0 right-0 top-full z-40 mt-2 origin-top scale-95 opacity-0 transition-all duration-200 ease-out">
            <div class="overflow-hidden rounded-2xl bg-white/90 backdrop-blur-xl shadow-lg">
                <div class="grid grid-cols-2">
                    <?php foreach ($item->children as $child) : ?>
                    <a href="<?php echo esc_url($child->url); ?>" class="flex border-b border-slate-100 p-6">
                        <span class="font-medium text-slate-900"><?php echo esc_html($child->title); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Mobile menu -->
        <div id="snel-mobile-menu"
            class="invisible absolute left-0 right-0 top-full mt-2 max-h-0 overflow-hidden rounded-2xl border border-transparent bg-white/80 opacity-0 shadow-lg backdrop-blur-xl transition-all duration-300 ease-out md:hidden">
            <nav class="flex flex-col p-4">
                <?php foreach ($menu_tree as $item) :
                    $is_active = ('/' . trim(parse_url($item->url, PHP_URL_PATH), '/') === $current_path);
                ?>
                    <a href="<?php echo esc_url($item->url); ?>"
                        class="rounded-lg px-4 py-3 text-sm transition-colors <?php echo $is_active ? 'bg-gradient-to-r from-blue-500/10 to-violet-500/10' : 'hover:bg-gray-100'; ?>">
                        <span class="<?php echo $is_active ? $nav_active_classes : 'font-medium text-gray-800'; ?>">
                            <?php echo esc_html($item->title); ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <script>
        (function () {
            // Language switcher popover
            var langBtn = document.getElementById('snel-lang-btn');
            var langPop = document.getElementById('snel-lang-popover');
            var langChev = document.getElementById('snel-lang-chevron');
            if (langBtn && langPop) {
                function openLang() {
                    langPop.classList.remove('invisible', 'opacity-0', 'scale-95');
                    langPop.classList.add('opacity-100', 'scale-100');
                    langBtn.setAttribute('aria-expanded', 'true');
                    if (langChev) langChev.style.transform = 'rotate(180deg)';
                }
                function closeLang() {
                    langPop.classList.add('invisible', 'opacity-0', 'scale-95');
                    langPop.classList.remove('opacity-100', 'scale-100');
                    langBtn.setAttribute('aria-expanded', 'false');
                    if (langChev) langChev.style.transform = '';
                }
                langBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    langPop.classList.contains('invisible') ? openLang() : closeLang();
                });
                document.addEventListener('click', closeLang);
                langPop.addEventListener('click', function (e) { e.stopPropagation(); });
            }
        })();
        (function () {
            var nav = document.getElementById('snel-nav');
            if (nav) {
                var pill = document.createElement('span');
                pill.setAttribute('aria-hidden', 'true');
                pill.style.cssText = 'position:absolute;border-radius:9999px;background:rgb(243 244 246);pointer-events:none;opacity:0;transition:left .18s ease,top .18s ease,width .18s ease,height .18s ease,opacity .12s ease;z-index:0;';
                nav.insertBefore(pill, nav.firstChild);
                var pillTimer;
                nav.querySelectorAll('a, button').forEach(function (item) {
                    item.style.position = 'relative';
                    item.style.zIndex = '1';
                    item.addEventListener('mouseenter', function () {
                        clearTimeout(pillTimer);
                        var nr = nav.getBoundingClientRect(), ir = item.getBoundingClientRect();
                        pill.style.left = (ir.left - nr.left) + 'px';
                        pill.style.top = (ir.top - nr.top) + 'px';
                        pill.style.width = ir.width + 'px';
                        pill.style.height = ir.height + 'px';
                        pill.style.opacity = '1';
                    });
                });
                nav.addEventListener('mouseleave', function () {
                    pillTimer = setTimeout(function () { pill.style.opacity = '0'; }, 120);
                });
            }

            var triggers = document.querySelectorAll('[data-dropdown-trigger]');
            triggers.forEach(function (trigger) {
                var key = trigger.dataset.dropdownTrigger;
                var panel = document.getElementById('snel-dropdown-' + key);
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
