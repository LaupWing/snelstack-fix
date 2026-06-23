<?php defined('ABSPATH') || exit; ?>
<style>
@keyframes snel-line-in {
	from { opacity: 0; transform: translateX(-6px); }
	to   { opacity: 1; transform: translateX(0); }
}
@keyframes snel-stat-in {
	from { opacity: 0; transform: translateY(6px); }
	to   { opacity: 1; transform: translateY(0); }
}
.snel-line-in { animation: snel-line-in 0.3s ease both; }
.snel-stat-in { animation: snel-stat-in 0.4s ease both; }
</style>
<div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/[0.03] overflow-hidden" style="background:#020617">
	<div class="flex items-center gap-2 px-4 py-3 border-b border-white/10 bg-white/[0.04]">
		<span class="h-2.5 w-2.5 rounded-full bg-red-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-yellow-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-green-400/50"></span>
		<div class="ml-2 flex-1 rounded-md bg-white/5 border border-white/10 px-3 py-1 text-xs text-white/25 font-mono">dashboard.jouwbedrijf.nl</div>
	</div>
	<div class="p-5 space-y-3 font-mono text-xs">
		<?php
		$lines = [
			['num' => '01', 'html' => '<span class="text-white/30">import</span> <span class="text-sky-300">Dashboard</span> <span class="text-white/30">from</span> <span class="text-teal-300">\'./components\'</span>', 'delay' => '0.05s'],
			['num' => '02', 'html' => '', 'delay' => '0.1s'],
			['num' => '03', 'html' => '<span class="text-pink-400">function</span> <span class="text-sky-300">App</span><span class="text-white/50">() {</span>', 'delay' => '0.15s'],
			['num' => '04', 'html' => '<span class="pl-4 text-white/30">return</span> <span class="text-white/50">(</span>', 'delay' => '0.2s'],
			['num' => '05', 'html' => '<span class="pl-8 text-teal-300">&lt;Dashboard</span> <span class="text-sky-300">data</span><span class="text-white/40">=</span><span class="text-amber-300">{leads}</span> <span class="inline-block w-0.5 h-3.5 bg-teal-300 align-middle animate-pulse"></span>', 'delay' => '0.25s'],
			['num' => '06', 'html' => '<span class="pl-8 text-sky-300">realtime</span><span class="text-white/40">=</span><span class="text-amber-300">{true}</span>', 'delay' => '0.3s'],
			['num' => '07', 'html' => '<span class="pl-8 text-teal-300">/&gt;</span>', 'delay' => '0.35s'],
			['num' => '08', 'html' => '<span class="pl-4 text-white/50">)</span>', 'delay' => '0.4s'],
			['num' => '09', 'html' => '<span class="text-white/50">}</span>', 'delay' => '0.45s'],
		];
		foreach ($lines as $line) : ?>
		<div class="snel-line-in flex gap-2" style="animation-delay:<?php echo $line['delay']; ?>">
			<span class="text-violet-400 shrink-0"><?php echo $line['num']; ?></span>
			<?php echo $line['html']; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="border-t border-white/10 px-5 py-4">
		<div class="grid grid-cols-3 gap-2">
			<div class="snel-stat-in rounded-xl border border-white/10 bg-white/5 p-3 text-center" style="animation-delay:0.5s">
				<div class="text-lg font-bold text-sky-400 tabular-nums">247</div>
				<div class="text-[10px] text-white/35 mt-0.5">Leads vandaag</div>
			</div>
			<div class="snel-stat-in rounded-xl border border-teal-300/20 bg-teal-300/5 p-3 text-center" style="animation-delay:0.6s">
				<div class="text-lg font-bold text-teal-300 tabular-nums">98%</div>
				<div class="text-[10px] text-white/35 mt-0.5">Uptime</div>
			</div>
			<div class="snel-stat-in rounded-xl border border-white/10 bg-white/5 p-3 text-center" style="animation-delay:0.7s">
				<div class="text-lg font-bold text-violet-400 tabular-nums">1.2s</div>
				<div class="text-[10px] text-white/35 mt-0.5">Laadtijd</div>
			</div>
		</div>
	</div>
</div>
