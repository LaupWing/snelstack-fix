<?php
defined('ABSPATH') || exit;

if (! function_exists('snel_mesh')) {
    function snel_mesh(string $fade = 'from-white'): string
    {
        $uid = wp_unique_id('snel-mesh-');
        ob_start();
        ?>
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <canvas id="<?php echo esc_attr($uid); ?>" class="absolute inset-0 h-full w-full" aria-hidden="true"></canvas>
            <div class="absolute inset-0 bg-gradient-to-t <?php echo esc_attr($fade); ?>"></div>
            <script>
            (function () {
                var canvas = document.getElementById('<?php echo esc_js($uid); ?>');
                if (!canvas) return;
                var ctx = canvas.getContext('2d');
                // Centers derived from CSS: left_% × W + 368px (half of 46rem blob)
                // At W=1500px: [-12%+368=188→12.5%, 18%+368=638→42.5%, 42%+368=998→66.5%, 62%+368=1298→86.5%, 88%+368=1688→112.5%]
                // cy from top_% × 384px + 368px, as fraction of band height (384px)
                var blobs = [
                    { cx: -0.15, cy: -0.1, opacity: 1.0, color: [167, 139, 250] }, // violet-400
                    { cx:  0.25, cy:  0.0, opacity: 1.0, color: [56,  189, 248] }, // sky-400
                    { cx:  0.55, cy: -0.1, opacity: 1.0, color: [244, 114, 182] }, // pink-400
                    { cx:  0.80, cy:  0.0, opacity: 0.5, color: [252, 165, 165] }, // red-300 — toned down
                    { cx:  0.95, cy: -0.1, opacity: 1.0, color: [94,  234, 212] }, // teal-300 — more inward
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
                        var o  = b.opacity;
                        g.addColorStop(0,    'rgba(' + c + ',' + (0.17 * o) + ')');
                        g.addColorStop(0.2,  'rgba(' + c + ',' + (0.10 * o) + ')');
                        g.addColorStop(0.45, 'rgba(' + c + ',' + (0.04 * o) + ')');
                        g.addColorStop(0.7,  'rgba(' + c + ',' + (0.01 * o) + ')');
                        g.addColorStop(1,    'rgba(' + c + ',0)');
                        ctx.fillStyle = g;
                        ctx.fillRect(0, 0, w, h);
                    });
                    requestAnimationFrame(draw);
                }

                resize();
                window.addEventListener('resize', resize);
                requestAnimationFrame(draw);
            })();
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}

if (! function_exists('snel_section_padding')) {
    function snel_section_padding(array $attributes): string
    {
        $size   = $attributes['size']          ?? 'md';
        $no_top = ! empty($attributes['disableTop']);
        $no_bot = ! empty($attributes['disableBottom']);

        $top    = ['sm' => 'pt-12 lg:pt-16', 'md' => 'pt-20 lg:pt-28', 'lg' => 'pt-24 lg:pt-32'];
        $bottom = ['sm' => 'pb-12 lg:pb-16', 'md' => 'pb-20 lg:pb-28', 'lg' => 'pb-24 lg:pb-32'];

        $parts = [];
        if (! $no_top) $parts[] = $top[$size]    ?? $top['md'];
        if (! $no_bot) $parts[] = $bottom[$size] ?? $bottom['md'];

        return implode(' ', $parts);
    }
}

if (! function_exists('snel_section_class')) {
    function snel_section_class(array $attributes, string $key = 'bg'): string
    {
        $val = $attributes[$key] ?? 'white';
        if ($val === 'dark')   return 'is-dark';
        if ($val === 'canvas') return 'is-dark bg-canvas';
        return 'bg-white';
    }
}

if (! function_exists('snel_section_style')) {
    function snel_section_style(array $attributes, string $key = 'bg'): string
    {
        $val = $attributes[$key] ?? 'white';
        if ($val === 'dark')   return ' style="background-color:#2e1065"';
        if ($val === 'canvas') return ' style="background-color:#020617"';
        return '';
    }
}
