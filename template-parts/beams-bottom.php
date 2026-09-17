<?php
/**
 * Beams rising from the bottom with the drifting colour blobs behind them.
 * Absolutely positioned: the parent must be `relative` (and ideally `isolate`),
 * with its content on a higher z-index. Used by the site footer and by any panel
 * with "Beams onderin" switched on.
 */

defined('ABSPATH') || exit;
?>
<?php // Fade out with a mask, not a solid overlay: an overlay paints its top edge in
      // the footer colour, which cuts a hard seam into whatever sits behind it (a panel's top band). ?>
<div class="pointer-events-none absolute inset-x-0 bottom-0 z-0 h-[70%] overflow-hidden"
     style="-webkit-mask-image:linear-gradient(to bottom,transparent,#000);mask-image:linear-gradient(to bottom,transparent,#000)">
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

        // This script runs inline, before the parent section's content is laid
        // out, so inside a panel the first measurement is 0 high. Re-measure once
        // the real size is known (and on any later resize).
        if ('ResizeObserver' in window) {
            new ResizeObserver(function () { resize(); if (reduce) draw(); }).observe(canvas);
        }
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
</div>
