</main>

<?php
defined('SNEL_BEAMS_COUNT')      || define('SNEL_BEAMS_COUNT', 50);
defined('SNEL_BEAMS_BASE_COUNT') || define('SNEL_BEAMS_BASE_COUNT', 60);

if (! function_exists('snel_beam_path')) {
    function snel_beam_path(int $i): string
    {
        $dx = 7 * $i;
        $dy = -8 * $i;
        $p  = fn($x, $y) => ($x + $dx) . ' ' . ($y + $dy);
        return 'M' . $p(-380, -189) . 'C' . $p(-380, -189) . ' ' . $p(-312, 216) . ' ' . $p(152, 343)
            . 'C' . $p(616, 470) . ' ' . $p(684, 875) . ' ' . $p(684, 875);
    }
}

if (! function_exists('snel_beams_svg')) {
    function snel_beams_svg(?string $uid = null, bool $flip = false): string
    {
        $uid  = $uid ?: wp_unique_id('snel-beam-');
        $base = '';
        for ($i = 0; $i < SNEL_BEAMS_BASE_COUNT; $i++) {
            $base .= snel_beam_path($i);
        }
        ob_start();
        ?>
        <svg class="pointer-events-none absolute inset-0 h-full w-full" width="100%" height="100%" viewBox="0 0 696 316" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"<?php echo $flip ? ' style="transform:scale(-1,-1)"' : ''; ?>>
            <path d="<?php echo esc_attr($base); ?>" stroke="url(#<?php echo esc_attr($uid); ?>-base)" stroke-opacity="0.2" stroke-width="0.5"></path>
            <?php for ($i = 0; $i < SNEL_BEAMS_COUNT; $i++) : ?>
                <path d="<?php echo esc_attr(snel_beam_path($i)); ?>" stroke="url(#<?php echo esc_attr($uid); ?>-<?php echo $i; ?>)" stroke-opacity="0.8" stroke-width="1.5"></path>
            <?php endfor; ?>
            <defs>
                <?php for ($i = 0; $i < SNEL_BEAMS_COUNT; $i++) :
                    $dur   = 10 + (($i * 7) % 11);
                    $begin = '-' . (($i * 3) % 10) . 's';
                    $y2    = 93 + ($i % 8);
                    ?>
                    <linearGradient id="<?php echo esc_attr($uid); ?>-<?php echo $i; ?>" x1="0%" y1="0%" x2="0%" y2="0%">
                        <animate attributeName="x1" values="0%;100%" dur="<?php echo $dur; ?>s" begin="<?php echo esc_attr($begin); ?>" repeatCount="indefinite"></animate>
                        <animate attributeName="x2" values="0%;95%" dur="<?php echo $dur; ?>s" begin="<?php echo esc_attr($begin); ?>" repeatCount="indefinite"></animate>
                        <animate attributeName="y1" values="0%;100%" dur="<?php echo $dur; ?>s" begin="<?php echo esc_attr($begin); ?>" repeatCount="indefinite"></animate>
                        <animate attributeName="y2" values="0%;<?php echo $y2; ?>%" dur="<?php echo $dur; ?>s" begin="<?php echo esc_attr($begin); ?>" repeatCount="indefinite"></animate>
                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0"></stop>
                        <stop offset="10%" stop-color="#38bdf8"></stop>
                        <stop offset="50%" stop-color="#a78bfa"></stop>
                        <stop offset="90%" stop-color="#f472b6"></stop>
                        <stop offset="100%" stop-color="#f472b6" stop-opacity="0"></stop>
                    </linearGradient>
                <?php endfor; ?>
                <radialGradient id="<?php echo esc_attr($uid); ?>-base" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(352 34) rotate(90) scale(555 1560.62)">
                    <stop offset="0.0666667" stop-color="#64748b"></stop>
                    <stop offset="0.243243" stop-color="#64748b"></stop>
                    <stop offset="0.43594" stop-color="white" stop-opacity="0"></stop>
                </radialGradient>
            </defs>
        </svg>
        <?php
        return ob_get_clean();
    }
}

if (! function_exists('snel_stack_icon')) {
    function snel_stack_icon(int $corner = 0): string
    {
        $cols = ['#5eead4', '#38bdf8', '#a78bfa', '#f472b6', '#fca5a5'];
        shuffle($cols);
        $cols = array_slice($cols, 0, 3);
        $ys   = [14, 9.5, 5];

        $svg = '<svg viewBox="0 0 24 24" class="size-full overflow-visible" xmlns="http://www.w3.org/2000/svg">';
        foreach ($cols as $i => $c) {
            $delay = -($corner * 1500 + $i * 6000);
            $svg  .= '<g class="snel-stack-layer" style="animation-delay:' . $delay . 'ms">'
                . '<rect x="-7" y="-7" width="14" height="14" rx="5" transform="translate(12 ' . $ys[$i] . ') scale(1 0.62) rotate(45)" fill="' . $c . '"/>'
                . '</g>';
        }
        return $svg . '</svg>';
    }
}

$email       = snel_business('email');
$site_name   = snel_business('name') ?: get_bloginfo('name');
$contact_url = get_permalink(get_page_by_path('contact')) ?: '#contact';

$arrow_up_right = '<path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 0 1.06 0l7.22-7.22v5.69a.75.75 0 0 0 1.5 0v-7.5a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0 0 1.5h5.69l-7.22 7.22a.75.75 0 0 0 0 1.06Z" clip-rule="evenodd"/>';
$cta_icon = '<span class="relative block size-5 overflow-hidden text-slate-950">'
    . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="absolute inset-0 size-5 transition-transform duration-300 ease-out group-hover:-translate-y-[150%]">' . $arrow_up_right . '</svg>'
    . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="absolute inset-0 size-5 translate-y-[150%] transition-transform duration-300 ease-out group-hover:translate-y-0">' . $arrow_up_right . '</svg>'
    . '</span>';

$whatsapp_url = get_theme_mod('snel_whatsapp_url', '#');
$privacy_page = get_page_by_path('privacybeleid');
$av_page      = get_page_by_path('algemene-voorwaarden');
?>

<footer class="relative flex min-h-[100dvh] flex-col justify-center overflow-hidden bg-slate-950">

    <?php /* Bordered frame + stack corners */ ?>
    <div class="pointer-events-none absolute inset-x-0 top-16 bottom-[53px] m-4 md:m-8 xl:m-16">
        <div class="absolute left-0 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>
        <div class="absolute right-0 top-4 bottom-4 w-px bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>
        <div class="absolute left-4 right-4 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
        <div class="absolute left-4 right-4 bottom-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

        <div class="pointer-events-auto absolute -top-2.5 -left-2.5 size-5"><?php echo snel_stack_icon(0); ?></div>
        <div class="pointer-events-auto absolute -top-2.5 -right-2.5 size-5"><?php echo snel_stack_icon(1); ?></div>
        <div class="pointer-events-auto absolute -bottom-2.5 -left-2.5 size-5"><?php echo snel_stack_icon(3); ?></div>
        <div class="pointer-events-auto absolute -bottom-2.5 -right-2.5 size-5"><?php echo snel_stack_icon(2); ?></div>
    </div>

    <?php /* Main content */ ?>
    <div class="relative z-20 px-4 pb-16 pt-20 md:px-8 md:pt-28 xl:px-16 2xl:px-32">
        <div class="mx-auto flex w-full flex-col items-center space-y-10 md:w-4/5 lg:w-3/5">

            <h2 class="text-center text-[calc(1.5rem+6vw)] font-bold leading-tight text-white lg:text-[calc(2rem+4vw)]">
                <?php echo esc_html(snel__(get_theme_mod('snel_footer_headline', 'Samen iets bouwen?'))); ?>
            </h2>

            <p class="mx-auto max-w-2xl text-balance text-center text-base text-white/60 sm:text-lg">
                <?php echo esc_html(snel__(get_theme_mod('snel_footer_subtext', 'Benieuwd wat we samen kunnen bouwen? Neem gerust contact op.'))); ?>
                <?php if ($email) : ?>
                    <?php echo esc_html(snel__('Stuur een mail naar')); ?>
                    <a class="mx-1 font-medium text-brand-primary transition hover:text-brand-primary/80 whitespace-nowrap"
                       href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                <?php endif; ?>
            </p>

            <div class="flex flex-col items-center justify-center gap-4 md:flex-row md:gap-6">

                <?php
                get_template_part('template-parts/gradient-button', null, [
                    'href'        => $contact_url,
                    'label'       => snel__('Start een gesprek'),
                    'icon'        => $cta_icon,
                    'face_class'  => 'px-6 text-base',
                    'outer_class' => 'h-12',
                ]);
                ?>

                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex">
                    <span class="group relative inline-flex animate-glow-pulse cursor-pointer overflow-hidden rounded-full p-[3px] transition-transform hover:scale-[1.02] active:scale-[0.98] h-12">
                        <span class="snel-gradient-ring absolute inset-0 rounded-full"></span>
                        <span class="relative inline-flex items-center justify-center gap-3 whitespace-nowrap rounded-full bg-slate-950 pl-6 pr-2 text-base font-semibold text-white">
                            <?php echo esc_html(snel__('Stuur een WhatsApp')); ?>
                            <span class="flex size-8 items-center justify-center rounded-full bg-[#25D366] text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                    <path d="M11.951 0C5.335 0 .018 5.317.018 11.932c0 2.1.554 4.07 1.525 5.776L0 24l6.479-1.701a11.921 11.921 0 0 0 5.473 1.33C18.568 23.629 24 18.312 24 11.697 24 5.083 18.568 0 11.951 0zm.007 21.624a9.924 9.924 0 0 1-5.063-1.381l-.363-.215-3.765.988 1.004-3.671-.236-.377a9.886 9.886 0 0 1-1.517-5.285c0-5.485 4.465-9.947 9.953-9.947 5.487 0 9.951 4.462 9.951 9.947 0 5.485-4.466 9.941-9.964 9.941z"/>
                                </svg>
                            </span>
                        </span>
                    </span>
                </a>

            </div>

        </div>
    </div>

    <?php /* Bottom bar */ ?>
    <div class="absolute bottom-0 z-50 w-full border-t border-white/10 bg-slate-950/30 px-4 py-3 backdrop-blur-sm md:px-8 xl:px-16 2xl:px-32">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 text-center text-xs text-white/40 antialiased sm:text-sm lg:flex-row lg:text-left">
            <div>&copy;<?php echo esc_html(date('Y')); ?>&nbsp;&nbsp;·&nbsp;&nbsp;<?php echo esc_html($site_name); ?></div>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 lg:justify-end">
                <?php if ($privacy_page) : ?>
                    <a class="transition hover:text-white" href="<?php echo esc_url(get_permalink($privacy_page)); ?>"><?php echo esc_html(snel__('Privacybeleid')); ?></a>
                <?php endif; ?>
                <?php if ($av_page) : ?>
                    <a class="transition hover:text-white" href="<?php echo esc_url(get_permalink($av_page)); ?>"><?php echo esc_html(snel__('Algemene voorwaarden')); ?></a>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <a class="transition hover:text-white" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php /* Bottom decoration — beams from bottom-right, blobs peeking up */ ?>
    <div class="pointer-events-none absolute inset-x-0 bottom-0 z-0 h-[70%] overflow-hidden">
        <?php echo snel_beams_svg(null, true); ?>
        <?php
        $fuid = wp_unique_id('snel-footer-mesh-');
        ?>
        <canvas id="<?php echo esc_attr($fuid); ?>" class="absolute inset-0 h-full w-full" aria-hidden="true"></canvas>
        <script>
        (function () {
            var canvas = document.getElementById('<?php echo esc_js($fuid); ?>');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var blobs = [
                { cx: -0.25, cy: 1.15, color: [167, 139, 250] },
                { cx:  0.35, cy: 1.05, color: [56,  189, 248] },
                { cx:  0.55, cy: 1.15, color: [244, 114, 182] },
                { cx:  0.80, cy: 1.05, color: [252, 165, 165] },
                { cx:  1.25, cy: 1.15, color: [94,  234, 212] },
            ];
            var times = blobs.map(function (_, i) { return i * 2.1; });

            function resize() {
                canvas.width  = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
            }

            function draw() {
                var w = canvas.width, h = canvas.height;
                ctx.clearRect(0, 0, w, h);
                blobs.forEach(function (b, i) {
                    times[i] += 0.003;
                    var cx = b.cx * w + Math.sin(times[i] * 0.7) * w * 0.06;
                    var cy = b.cy * h + Math.cos(times[i] * 0.5) * h * 0.05;
                    var r  = Math.max(700, w * 0.85);
                    var g  = ctx.createRadialGradient(cx, cy, 0, cx, cy, r);
                    var c  = b.color.join(',');
                    g.addColorStop(0,    'rgba(' + c + ',0.28)');
                    g.addColorStop(0.2,  'rgba(' + c + ',0.18)');
                    g.addColorStop(0.45, 'rgba(' + c + ',0.07)');
                    g.addColorStop(0.7,  'rgba(' + c + ',0.02)');
                    g.addColorStop(1,    'rgba(' + c + ',0)');
                    ctx.fillStyle = g;
                    ctx.fillRect(0, 0, w, h);
                });
            }

            resize();
            window.addEventListener('resize', resize);

            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var rafId = null, onScreen = false;

            function loop() { draw(); rafId = requestAnimationFrame(loop); }
            function start() { if (rafId === null && onScreen && !document.hidden) rafId = requestAnimationFrame(loop); }
            function stop() { if (rafId !== null) { cancelAnimationFrame(rafId); rafId = null; } }

            // prefers-reduced-motion: paint a single static frame, no loop.
            if (reduce) { draw(); return; }

            // Pause when the tab is backgrounded.
            document.addEventListener('visibilitychange', function () {
                document.hidden ? stop() : start();
            });

            // Only animate while the footer is actually in (or near) the viewport.
            if ('IntersectionObserver' in window) {
                new IntersectionObserver(function (entries) {
                    onScreen = entries[0].isIntersecting;
                    onScreen ? start() : stop();
                }, { rootMargin: '200px' }).observe(canvas);
            } else {
                onScreen = true;
                start();
            }

            // Freeze during transient UI (e.g. mobile menu open) — see snelAnim.
            document.addEventListener('snel:anim', function (e) {
                (e.detail && e.detail.frozen) ? stop() : start();
            });
        })();
        </script>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 to-transparent"></div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
