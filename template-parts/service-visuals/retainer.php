<?php defined('ABSPATH') || exit; ?>
<style>
@keyframes snel-bar-grow {
	from { transform: scaleX(0); opacity: 0; }
	to   { transform: scaleX(1); opacity: 1; }
}
@keyframes snel-count-up {
	from { opacity: 0; transform: translateY(4px); }
	to   { opacity: 1; transform: translateY(0); }
}
.snel-bar-anim {
	transform-origin: left;
	animation: snel-bar-grow 0.4s ease both;
}
</style>
<div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/[0.03] overflow-hidden" style="background:#020617">
	<div class="px-5 py-5 border-b border-white/10">
		<div class="flex items-start justify-between">
			<div>
				<div class="text-xs font-medium text-white/40 uppercase tracking-wider mb-1">Uptime</div>
				<div class="text-4xl font-bold text-teal-300 tabular-nums" style="animation:snel-count-up 0.6s ease 0.2s both">99.97%</div>
				<div class="text-xs text-white/35 mt-1">Afgelopen 90 dagen</div>
			</div>
			<div class="flex items-center gap-2 rounded-full border border-teal-300/30 bg-teal-300/10 px-3 py-1.5">
				<span class="h-2 w-2 rounded-full bg-teal-300 animate-pulse inline-block shrink-0"></span>
				<span class="text-xs font-semibold text-teal-300">Alles online</span>
			</div>
		</div>
	</div>
	<div class="divide-y divide-white/[0.06]">
		<?php
		$rows = [
			['name' => 'jouwsite.nl',  'ms' => '342ms', 'pct' => '100%', 'color' => 'bg-teal-300',  'delay' => '0.1s'],
			['name' => 'webshop.nl',   'ms' => '218ms', 'pct' => '99.9%','color' => 'bg-teal-300',  'delay' => '0.2s'],
			['name' => 'platform.nl',  'ms' => '89ms',  'pct' => '99.8%','color' => 'bg-sky-400',   'delay' => '0.3s'],
		];
		foreach ($rows as $row) : ?>
		<div class="flex items-center gap-3 px-5 py-3" style="animation:snel-count-up 0.4s ease <?php echo $row['delay']; ?> both">
			<span class="h-2.5 w-2.5 rounded-full <?php echo $row['color']; ?> shrink-0"></span>
			<div class="flex-1 text-sm text-white/70 font-mono"><?php echo $row['name']; ?></div>
			<span class="text-xs text-white/35 font-mono"><?php echo $row['ms']; ?></span>
			<span class="text-xs font-semibold <?php echo str_replace('bg-', 'text-', $row['color']); ?> w-10 text-right"><?php echo $row['pct']; ?></span>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="px-5 py-4 border-t border-white/10">
		<div class="text-xs text-white/35 mb-2.5">Maandelijkse uptime</div>
		<div class="flex gap-1">
			<?php
			$months = [1,1,1,0,1,1,1,0,1,1,1,1];
			foreach ($months as $i => $full) :
				$bg    = $full ? '#5eead4' : '#f59e0b';
				$delay = round(0.05 * $i, 2) . 's';
			?>
			<div class="snel-bar-anim flex-1 h-6 rounded-sm" style="background:<?php echo $bg; ?>;opacity:<?php echo $full ? '0.55' : '0.75'; ?>;animation-delay:<?php echo $delay; ?>"></div>
			<?php endforeach; ?>
		</div>
		<div class="flex justify-between text-[10px] text-white/25 mt-1.5">
			<span>jan</span><span>apr</span><span>jul</span><span>okt</span><span>dec</span>
		</div>
	</div>
</div>
