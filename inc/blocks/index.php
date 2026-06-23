<?php
defined('ABSPATH') || exit;

const SNEL_BEAMS_COUNT      = 50;
const SNEL_BEAMS_BASE_COUNT = 60;

function snel_beam_path(int $i): string
{
    $dx = 7 * $i;
    $dy = -8 * $i;
    $p  = fn($x, $y) => ($x + $dx) . ' ' . ($y + $dy);
    return 'M' . $p(-380, -189) . 'C' . $p(-380, -189) . ' ' . $p(-312, 216) . ' ' . $p(152, 343)
        . 'C' . $p(616, 470) . ' ' . $p(684, 875) . ' ' . $p(684, 875);
}

function snel_beams_svg(?string $uid = null): string
{
    $uid  = $uid ?: wp_unique_id('snel-beam-');
    $base = '';
    for ($i = 0; $i < SNEL_BEAMS_BASE_COUNT; $i++) {
        $base .= snel_beam_path($i);
    }
    ob_start(); ?>
    <svg class="pointer-events-none absolute inset-0 h-full w-full" width="100%" height="100%" viewBox="0 0 696 316" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="<?php echo esc_attr($base); ?>" stroke="url(#<?php echo esc_attr($uid); ?>-base)" stroke-opacity="0.2" stroke-width="0.5"></path>
        <?php for ($i = 0; $i < SNEL_BEAMS_COUNT; $i++) : ?>
            <path d="<?php echo esc_attr(snel_beam_path($i)); ?>" stroke="url(#<?php echo esc_attr($uid); ?>-<?php echo $i; ?>)" stroke-opacity="0.8" stroke-width="1.5"></path>
        <?php endfor; ?>
        <defs>
            <?php for ($i = 0; $i < SNEL_BEAMS_COUNT; $i++) :
                $dur   = 10 + (($i * 7) % 11);
                $begin = '-' . (($i * 3) % 10) . 's';
                $y2    = 93 + ($i % 8); ?>
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
    <?php return ob_get_clean();
}

function snel_mesh(): string
{
    $blobs = [
        ['color' => 'bg-violet-400/50', 'anim' => 'animate-mesh-1', 'pos' => 'left-[-12%] top-[-35%]'],
        ['color' => 'bg-sky-400/50',    'anim' => 'animate-mesh-2', 'pos' => 'left-[18%] top-[-20%]'],
        ['color' => 'bg-pink-400/45',   'anim' => 'animate-mesh-3', 'pos' => 'left-[42%] top-[-30%]'],
        ['color' => 'bg-red-300/40',    'anim' => 'animate-mesh-2', 'pos' => 'left-[62%] top-[-18%]'],
        ['color' => 'bg-teal-300/55',   'anim' => 'animate-mesh-1', 'pos' => 'left-[88%] top-[-32%]'],
    ];
    ob_start(); ?>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 opacity-50">
            <?php foreach ($blobs as $b) : ?>
                <span class="absolute h-[46rem] w-[46rem] rounded-full blur-[140px] <?php echo esc_attr("{$b['color']} {$b['anim']} {$b['pos']}"); ?>"></span>
            <?php endforeach; ?>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-white"></div>
    </div>
    <?php return ob_get_clean();
}

function snel_background_open(array $args = []): void
{
    $position   = ($args['position'] ?? 'absolute') === 'relative' ? 'relative' : 'absolute';
    $band_class = $position === 'relative'
        ? 'pointer-events-none relative z-0 h-96 w-full overflow-hidden'
        : 'pointer-events-none absolute inset-x-0 top-0 z-0 h-96 overflow-hidden'; ?>
    <div class="relative isolate overflow-hidden bg-white">
        <div class="<?php echo esc_attr($band_class); ?>">
            <?php echo snel_beams_svg(); ?>
            <?php echo snel_mesh(); ?>
        </div>
        <div class="relative z-10">
    <?php
}

function snel_background_close(): void
{
    echo '</div></div>';
}

require_once __DIR__ . '/panel-frame.php';
