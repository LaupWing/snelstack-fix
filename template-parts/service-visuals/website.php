<?php defined('ABSPATH') || exit; ?>
<style>
@keyframes snel-shimmer {
	0%   { background-position: -400px 0; }
	100% { background-position: 400px 0; }
}
@keyframes snel-fade-up {
	from { opacity: 0; transform: translateY(8px); }
	to   { opacity: 1; transform: translateY(0); }
}
.snel-shimmer {
	background: linear-gradient(90deg, rgba(255,255,255,0.06) 25%, rgba(255,255,255,0.12) 50%, rgba(255,255,255,0.06) 75%);
	background-size: 800px 100%;
	animation: snel-shimmer 2s infinite linear;
}
.snel-fade-up { animation: snel-fade-up 0.5s ease both; }
</style>
<div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/[0.03] overflow-hidden" style="background:#020617">
	<div class="flex items-center gap-2 px-4 py-3 border-b border-white/10 bg-white/[0.04]">
		<span class="h-2.5 w-2.5 rounded-full bg-red-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-yellow-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-green-400/50"></span>
		<div class="ml-2 flex-1 rounded-md bg-white/5 border border-white/10 px-3 py-1 text-xs text-white/25 font-mono flex items-center gap-1">
			jouwbedrijf.nl
			<span class="inline-block w-px h-3 bg-white/40 ml-0.5 animate-pulse"></span>
		</div>
	</div>
	<div class="p-5 space-y-4">
		<div class="snel-fade-up flex items-center justify-between" style="animation-delay:0.1s">
			<div class="h-4 w-20 rounded-sm snel-shimmer"></div>
			<div class="flex gap-3">
				<div class="h-2 w-10 rounded-sm snel-shimmer"></div>
				<div class="h-2 w-10 rounded-sm snel-shimmer"></div>
				<div class="h-2 w-14 rounded-sm snel-shimmer"></div>
			</div>
		</div>
		<div class="pt-4 space-y-3">
			<div class="snel-fade-up h-3 w-3/4 rounded-sm bg-white/60" style="animation-delay:0.2s"></div>
			<div class="snel-fade-up h-3 w-1/2 rounded-sm bg-teal-300/50" style="animation-delay:0.3s"></div>
			<div class="snel-fade-up h-2 w-full rounded-sm snel-shimmer" style="animation-delay:0.35s"></div>
			<div class="snel-fade-up h-2 w-5/6 rounded-sm snel-shimmer" style="animation-delay:0.4s"></div>
			<div class="snel-fade-up pt-2 flex gap-3" style="animation-delay:0.5s">
				<div class="h-9 w-28 rounded-full bg-teal-300/70"></div>
				<div class="h-9 w-24 rounded-full border border-white/20 bg-transparent"></div>
			</div>
		</div>
		<div class="grid grid-cols-3 gap-2 pt-2">
			<div class="snel-fade-up rounded-xl border border-white/10 bg-white/5 p-3 space-y-2" style="animation-delay:0.55s">
				<div class="h-6 w-6 rounded-lg bg-sky-400/25"></div>
				<div class="h-2 w-full rounded-sm snel-shimmer"></div>
				<div class="h-2 w-4/5 rounded-sm snel-shimmer"></div>
			</div>
			<div class="snel-fade-up rounded-xl border border-violet-500/20 bg-violet-500/5 p-3 space-y-2" style="animation-delay:0.65s">
				<div class="h-6 w-6 rounded-lg bg-violet-400/30"></div>
				<div class="h-2 w-full rounded-sm snel-shimmer"></div>
				<div class="h-2 w-4/5 rounded-sm snel-shimmer"></div>
			</div>
			<div class="snel-fade-up rounded-xl border border-white/10 bg-white/5 p-3 space-y-2" style="animation-delay:0.75s">
				<div class="h-6 w-6 rounded-lg bg-pink-400/25"></div>
				<div class="h-2 w-full rounded-sm snel-shimmer"></div>
				<div class="h-2 w-4/5 rounded-sm snel-shimmer"></div>
			</div>
		</div>
	</div>
</div>
