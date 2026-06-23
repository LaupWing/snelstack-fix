<?php
defined('ABSPATH') || exit;

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

if (! function_exists('snel_panel_open')) {
    function snel_panel_open(array $args = []): void
    {
        $max   = $args['max'] ?? 'max-w-5xl';
        $extra = $args['class'] ?? '';
        $inner = $args['inner_class'] ?? '';
        $dark  = ! empty($args['dark']);

        $border_x = $dark ? 'via-white/10' : 'via-sky-400/70';
        $border_y = $dark ? 'via-white/10' : 'via-violet-500/70';

        $corners = [
            '-top-2.5 -left-2.5'     => 0,
            '-top-2.5 -right-2.5'    => 1,
            '-bottom-2.5 -left-2.5'  => 3,
            '-bottom-2.5 -right-2.5' => 2,
        ];
        ?>
        <div class="relative mx-auto w-full <?php echo esc_attr(trim("$max $extra")); ?> p-8 xl:p-14">
            <div class="pointer-events-none absolute left-4 right-4 top-0 h-px bg-gradient-to-r from-transparent <?php echo esc_attr($border_x); ?> to-transparent"></div>
            <div class="pointer-events-none absolute left-4 right-4 bottom-0 h-px bg-gradient-to-r from-transparent <?php echo esc_attr($border_y); ?> to-transparent"></div>
            <div class="pointer-events-none absolute top-4 bottom-4 left-0 w-px bg-gradient-to-b from-transparent <?php echo esc_attr($border_x); ?> to-transparent"></div>
            <div class="pointer-events-none absolute top-4 bottom-4 right-0 w-px bg-gradient-to-b from-transparent <?php echo esc_attr($border_y); ?> to-transparent"></div>
            <?php foreach ($corners as $pos => $i) : ?>
                <div class="absolute <?php echo esc_attr($pos); ?> size-5"><?php echo snel_stack_icon($i); ?></div>
            <?php endforeach; ?>
            <div class="relative z-10 flex flex-col <?php echo esc_attr($inner); ?>">
        <?php
    }
}

if (! function_exists('snel_panel_close')) {
    function snel_panel_close(): void
    {
        echo '</div></div>';
    }
}
