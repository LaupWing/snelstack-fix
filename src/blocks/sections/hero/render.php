<?php
defined('ABSPATH') || exit;

$heading    = $attributes['heading']    ?? '';
$subheading = $attributes['subheading'] ?? '';
$cta_label  = $attributes['ctaLabel']   ?? '';
$cta_url    = $attributes['ctaUrl']     ?? '#';

// Beam path: M x0,y0 C x1,y1 x2,y2 x3,y3  with dx=7*i, dy=-8*i
function snel_beam_d(int $i): string {
    $dx = 7 * $i; $dy = -8 * $i;
    return sprintf(
        'M %d,%d C %d,%d %d,%d %d,%d',
        -380 + $dx, -189 + $dy,
        -312 + $dx,  216 + $dy,
         152 + $dx,  343 + $dy,
         684 + $dx,  875 + $dy
    );
}
?>
<div class="pointer-events-none absolute inset-x-0 top-0 h-96 overflow-hidden" aria-hidden="true">
<svg viewBox="0 0 696 316" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
<defs>
    <!-- base gradient -->
    <linearGradient id="sbg" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%"   stop-color="#94a3b8" stop-opacity="0"/>
        <stop offset="20%"  stop-color="#94a3b8" stop-opacity="0.6"/>
        <stop offset="80%"  stop-color="#94a3b8" stop-opacity="0.6"/>
        <stop offset="100%" stop-color="#94a3b8" stop-opacity="0"/>
    </linearGradient>
    <!-- animated gradients, one per beam -->
    <?php for ($i = 0; $i < 50; $i++) :
        $dur  = (10 + ($i * 7) % 11) . 's';
        $off  = round(($i * 3 % 10) / (10 + ($i * 7) % 11), 2);
        $begin = $off > 0 ? "-{$off}s" : '0s';
    ?>
    <linearGradient id="sbm<?php echo $i; ?>" gradientUnits="userSpaceOnUse" x1="0%" y1="0%" x2="0%" y2="0%">
        <animate attributeName="x1" values="0%;100%" dur="<?php echo $dur; ?>" begin="<?php echo $begin; ?>" repeatCount="indefinite"/>
        <animate attributeName="x2" values="0%;95%"  dur="<?php echo $dur; ?>" begin="<?php echo $begin; ?>" repeatCount="indefinite"/>
        <animate attributeName="y1" values="0%;100%" dur="<?php echo $dur; ?>" begin="<?php echo $begin; ?>" repeatCount="indefinite"/>
        <animate attributeName="y2" values="0%;93%"  dur="<?php echo $dur; ?>" begin="<?php echo $begin; ?>" repeatCount="indefinite"/>
        <stop offset="0%"   stop-color="#38bdf8" stop-opacity="0"/>
        <stop offset="10%"  stop-color="#38bdf8"/>
        <stop offset="50%"  stop-color="#a78bfa"/>
        <stop offset="90%"  stop-color="#f472b6"/>
        <stop offset="100%" stop-color="#f472b6" stop-opacity="0"/>
    </linearGradient>
    <?php endfor; ?>
</defs>

<!-- base bundle -->
<g opacity="0.15" stroke="url(#sbg)" stroke-width="0.5" fill="none">
<?php for ($i = 0; $i < 60; $i++) : ?>
    <path d="<?php echo snel_beam_d($i); ?>"/>
<?php endfor; ?>
</g>

<!-- animated beams -->
<?php for ($i = 0; $i < 50; $i++) : ?>
<path d="<?php echo snel_beam_d($i); ?>" stroke="url(#sbm<?php echo $i; ?>)" stroke-width="1.5" fill="none" opacity="0.9"/>
<?php endfor; ?>

</svg>
</div>

<section class="relative bg-white px-4 pt-40 pb-20 md:px-8">
    <div class="mx-auto max-w-5xl flex flex-col gap-8">

        <p class="text-sm text-slate-500">Score 4.9 · op basis van 74 reviews</p>

        <?php if ($heading) : ?>
        <h1 class="max-w-4xl font-semibold text-slate-950 text-2xl/tight md:text-3xl/tight lg:text-5xl/tight">
            <?php echo wp_kses($heading, ['em' => [], 'strong' => []]); ?>
        </h1>
        <?php endif; ?>

        <?php if ($subheading) : ?>
        <p class="max-w-2xl text-lg text-slate-600">
            <?php echo wp_kses($subheading, ['em' => [], 'strong' => []]); ?>
        </p>
        <?php endif; ?>

        <?php if ($cta_label) : ?>
        <a href="<?php echo esc_url($cta_url); ?>" class="inline-flex h-12 w-fit items-center rounded-full bg-violet-600 px-6 text-base font-semibold text-white">
            <?php echo esc_html($cta_label); ?>
        </a>
        <?php endif; ?>

    </div>
</section>
