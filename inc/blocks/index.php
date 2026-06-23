<?php
defined('ABSPATH') || exit;

function snel_mesh(): string
{
    $blobs = [
        ['bg-violet-400/50', 'animate-mesh-1', 'left-[-12%] top-[-35%]'],
        ['bg-sky-400/50',    'animate-mesh-2', 'left-[18%] top-[-20%]'],
        ['bg-pink-400/45',   'animate-mesh-3', 'left-[42%] top-[-30%]'],
        ['bg-red-300/40',    'animate-mesh-2', 'left-[62%] top-[-18%]'],
        ['bg-teal-300/55',   'animate-mesh-1', 'left-[88%] top-[-32%]'],
    ];

    ob_start(); ?>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 opacity-50">
            <?php foreach ($blobs as [$color, $anim, $pos]) : ?>
                <span class="absolute h-[46rem] w-[46rem] rounded-full blur-[140px] <?php echo esc_attr("$color $anim $pos"); ?>"></span>
            <?php endforeach; ?>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-white"></div>
    </div>
    <?php return ob_get_clean();
}

require_once __DIR__ . '/panel-frame.php';
