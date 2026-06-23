<?php
/**
 * Panel frame — the reusable framed "card" wrapper.
 *
 * The shell shared by the snel/hero and snel/panel blocks (and the footer): a
 * max-w-5xl card with hairline gradient borders and four animated brand "stack"
 * corners. Drop content between snel_panel_open() and snel_panel_close(); place
 * the pair INSIDE snel_background_open()/close() so it sits on the beams + mesh.
 *
 * Required from inc/blocks/index.php (which is auto-loaded).
 *
 * @package Snel
 */

defined('ABSPATH') || exit;

/**
 * The brand "stack" corner icon: three isometric rounded diamond plates stacked
 * back-to-front in random brand colours. Each plate's animation phase is offset
 * (and each corner offset by a quarter of the cycle) so only ONE corner "waves"
 * at a time — see the .snel-stack-layer / stack-pulse rule in theme.css.
 *
 * @param int $corner 0..3 — staggers this corner's wave against the others.
 */
function snel_stack_icon(int $corner = 0): string
{
    $cols = ['#5eead4', '#38bdf8', '#a78bfa', '#f472b6', '#fca5a5'];
    shuffle($cols);
    $cols = array_slice($cols, 0, 3);
    $ys = [14, 9.5, 5]; // back-to-front (bottom → top), tight overlap = thick stack

    $svg = '<svg viewBox="0 0 24 24" class="size-full overflow-visible" xmlns="http://www.w3.org/2000/svg">';
    foreach ($cols as $i => $c) {
        $delay = -($corner * 1500 + $i * 6000);
        $svg .= '<g class="snel-stack-layer" style="animation-delay:' . $delay . 'ms">'
            . '<rect x="-7" y="-7" width="14" height="14" rx="5" transform="translate(12 ' . $ys[$i] . ') scale(1 0.62) rotate(45)" fill="' . $c . '"/>'
            . '</g>';
    }
    return $svg . '</svg>';
}

/**
 * Open the framed "panel" card: hairline gradient borders + animated brand
 * "stack" corners around a max-w-5xl card. Content goes between open/close.
 * Place INSIDE snel_background_open()/close() so it sits on the beams + mesh.
 *
 * @param array $args {
 *   @type string $max         Max-width utility for the card (default 'max-w-5xl').
 *   @type string $class       Extra classes for the card.
 *   @type string $inner_class Extra classes for the content column (e.g. gap-8).
 *   @type bool   $dark        Dark theme → white borders instead of sky/violet.
 * }
 */
function snel_panel_open(array $args = []): void
{
    $max   = $args['max'] ?? 'max-w-5xl';
    $extra = $args['class'] ?? '';
    $inner = $args['inner_class'] ?? ''; // extra classes for the content column (e.g. gap-8)
    $dark  = ! empty($args['dark']);

    // Borders: sky/violet on light; hairline white on dark.
    $border_x = $dark ? 'via-white/10' : 'via-sky-400/70';
    $border_y = $dark ? 'via-white/10' : 'via-violet-500/70';

    // Corner position → stagger index (one corner waves at a time, like the hero).
    $corners = [
        '-top-2.5 -left-2.5'     => 0,
        '-top-2.5 -right-2.5'    => 1,
        '-bottom-2.5 -left-2.5'  => 3,
        '-bottom-2.5 -right-2.5' => 2,
    ];
    ?>
    <div class="relative mx-auto w-full <?php echo esc_attr(trim("$max $extra")); ?> p-8 xl:p-14">

        <?php // Hairline borders — sky/violet (light) or white (dark). ?>
        <div class="pointer-events-none absolute left-4 right-4 top-0 h-px bg-gradient-to-r from-transparent <?php echo esc_attr($border_x); ?> to-transparent"></div>
        <div class="pointer-events-none absolute left-4 right-4 bottom-0 h-px bg-gradient-to-r from-transparent <?php echo esc_attr($border_y); ?> to-transparent"></div>
        <div class="pointer-events-none absolute top-4 bottom-4 left-0 w-px bg-gradient-to-b from-transparent <?php echo esc_attr($border_x); ?> to-transparent"></div>
        <div class="pointer-events-none absolute top-4 bottom-4 right-0 w-px bg-gradient-to-b from-transparent <?php echo esc_attr($border_y); ?> to-transparent"></div>

        <?php // Corner "stacks" — random brand colours; corners take turns waving. ?>
        <?php foreach ($corners as $pos => $i) : ?>
            <div class="absolute <?php echo esc_attr($pos); ?> size-5"><?php echo snel_stack_icon($i); ?></div>
        <?php endforeach; ?>

        <div class="relative z-10 flex flex-col <?php echo esc_attr($inner); ?>">
    <?php
}

/**
 * Close the panel card opened by snel_panel_open().
 */
function snel_panel_close(): void
{
    echo '</div></div>';
}
