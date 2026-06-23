<?php
defined('ABSPATH') || exit;

defined('SNEL_BEAMS_COUNT')      || define('SNEL_BEAMS_COUNT',      50);
defined('SNEL_BEAMS_BASE_COUNT') || define('SNEL_BEAMS_BASE_COUNT', 60);

function snel_beam_path(int $i): string
{
    $dx = 7 * $i;
    $dy = -8 * $i;
    $p  = fn($x, $y) => ($x + $dx) . ' ' . ($y + $dy);

    return 'M' . $p(-380, -189) . 'C' . $p(-380, -189) . ' ' . $p(-312, 216) . ' ' . $p(152, 343)
        . 'C' . $p(616, 470) . ' ' . $p(684, 875) . ' ' . $p(684, 875);
}

function snel_beams_svg(?string $uid = null, bool $flip = false): string
{
    $uid = $uid ?: wp_unique_id('snel-beam-');

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

require_once __DIR__ . '/panel-frame.php';
