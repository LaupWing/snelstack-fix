<?php
defined('ABSPATH') || exit;

if (! function_exists('snel_background_open')) {
    function snel_background_open(array $args = []): void
    {
        $position = ($args['position'] ?? 'absolute') === 'relative' ? 'relative' : 'absolute';
        $backdrop = $args['backdrop'] ?? 'white';
        $extra    = $args['class'] ?? '';
        $fade     = $args['fade'] ?? 'from-white';

        $backdrop_class = [
            'white'       => 'bg-white',
            'dark'        => 'bg-neutral-950',
            'transparent' => '',
        ][$backdrop] ?? 'bg-white';

        $show_beams    = $args['beams']    ?? true;
        $show_gradient = $args['gradient'] ?? true;

        $band_class = $position === 'relative'
            ? 'pointer-events-none relative z-0 h-96 w-full overflow-hidden'
            : 'pointer-events-none absolute inset-x-0 top-0 z-0 h-96 overflow-hidden';
        ?>
        <div class="relative isolate overflow-hidden <?php echo esc_attr(trim("$backdrop_class $extra")); ?>">
            <div class="<?php echo esc_attr($band_class); ?>">
                <?php if ($show_beams) echo snel_beams_svg(); ?>
            </div>
            <div class="relative z-10">
        <?php
    }
}

if (! function_exists('snel_background_close')) {
    function snel_background_close(): void
    {
        echo '</div></div>';
    }
}
