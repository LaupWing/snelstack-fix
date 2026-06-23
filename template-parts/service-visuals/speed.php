<?php defined('ABSPATH') || exit; ?>
<style>
@keyframes snel-loadbar {
	0%   { transform: scaleX(0); opacity: 1; }
	70%  { transform: scaleX(1); opacity: 1; }
	100% { transform: scaleX(1); opacity: 0; }
}
@keyframes snel-score-in {
	from { stroke-dashoffset: 163; }
	to   { stroke-dashoffset: 17; }
}
.snel-sv-loadbar    { animation: snel-loadbar 1.6s cubic-bezier(0.25,0.8,0.25,1) 0.3s both; transform-origin: left; }
.snel-sv-score-ring { animation: snel-score-in 1.2s cubic-bezier(0.4,0,0.2,1) 0.6s both; }
</style>
<div class="w-full max-w-md rounded-3xl overflow-hidden border border-white/10 shadow-2xl" style="background:#020617">

	<!-- Blurred colour blobs -->
	<div class="pointer-events-none absolute inset-0 overflow-hidden rounded-3xl" aria-hidden="true">
		<div class="absolute -top-16 -left-16 w-64 h-64 rounded-full blur-3xl opacity-30" style="background:#5eead4"></div>
		<div class="absolute top-8 right-0 w-48 h-48 rounded-full blur-3xl opacity-20" style="background:#a78bfa"></div>
		<div class="absolute bottom-0 left-1/3 w-56 h-56 rounded-full blur-3xl opacity-20" style="background:#38bdf8"></div>
		<div class="absolute -bottom-8 right-8 w-40 h-40 rounded-full blur-3xl opacity-15" style="background:#f472b6"></div>
	</div>

	<!-- Browser chrome -->
	<div class="relative flex items-center gap-2 px-4 py-3 border-b border-white/10" style="background:rgba(255,255,255,0.04)">
		<span class="h-2.5 w-2.5 rounded-full bg-red-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-yellow-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-green-400/50"></span>
		<div class="ml-2 flex-1 rounded-md border border-white/10 px-3 py-1 text-xs text-white/30 font-mono flex items-center justify-between" style="background:rgba(255,255,255,0.05)">
			<span>jouwbedrijf.nl</span>
			<span class="font-sans font-semibold" style="color:#5eead4">0.8s &#9889;</span>
		</div>
		<!-- load bar -->
		<div class="absolute bottom-0 left-0 right-0 h-0.5" style="background:rgba(255,255,255,0.05)">
			<div class="snel-sv-loadbar absolute inset-0" style="background:linear-gradient(to right,#5eead4,#38bdf8,#a78bfa)"></div>
		</div>
	</div>

	<!-- Lighthouse score -->
	<div class="relative flex flex-col items-center py-7 gap-2">
		<div class="relative flex items-center justify-center">
			<svg class="w-28 h-28 -rotate-90" viewBox="0 0 60 60" aria-hidden="true">
				<circle cx="30" cy="30" r="26" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="4"/>
				<circle class="snel-sv-score-ring" cx="30" cy="30" r="26" fill="none"
					stroke="url(#snel-speed-g)" stroke-width="4"
					stroke-linecap="round" stroke-dasharray="163" stroke-dashoffset="163"/>
				<defs>
					<linearGradient id="snel-speed-g" x1="0%" y1="0%" x2="100%" y2="0%">
						<stop offset="0%" stop-color="#5eead4"/>
						<stop offset="100%" stop-color="#38bdf8"/>
					</linearGradient>
				</defs>
			</svg>
			<div class="absolute flex flex-col items-center">
				<span class="text-3xl font-bold leading-none" style="color:#5eead4">98</span>
				<span class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.4)">/&nbsp;100</span>
			</div>
		</div>
		<div class="text-xs font-medium uppercase tracking-widest" style="color:rgba(255,255,255,0.4)">Performance</div>
	</div>

	<!-- Core Web Vitals -->
	<div class="relative grid grid-cols-3" style="border-top:1px solid rgba(255,255,255,0.07);gap:1px;background:rgba(255,255,255,0.07)">
		<div class="px-4 py-3 text-center" style="background:#020617">
			<div class="text-base font-semibold" style="color:#5eead4">0.9s</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">LCP</div>
		</div>
		<div class="px-4 py-3 text-center" style="background:#020617">
			<div class="text-base font-semibold" style="color:#38bdf8">12ms</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">FID</div>
		</div>
		<div class="px-4 py-3 text-center" style="background:#020617">
			<div class="text-base font-semibold" style="color:#a78bfa">0.02</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">CLS</div>
		</div>
	</div>

	<!-- Bottom stats -->
	<div class="relative flex items-center" style="border-top:1px solid rgba(255,255,255,0.07);gap:1px;background:rgba(255,255,255,0.07)">
		<div class="flex-1 px-4 py-3 text-center" style="background:#020617">
			<div class="text-sm font-semibold" style="color:rgba(255,255,255,0.8)">2.6s</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">Eerste indruk</div>
		</div>
		<div class="flex-1 px-4 py-3 text-center" style="background:#020617">
			<div class="text-sm font-semibold" style="color:rgba(255,255,255,0.8)">94%</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">Visueel bepaald</div>
		</div>
		<div class="flex-1 px-4 py-3 text-center" style="background:#020617">
			<div class="text-sm font-semibold" style="color:#5eead4">Top 3%</div>
			<div class="text-[10px] mt-0.5" style="color:rgba(255,255,255,0.35)">NL snelheid</div>
		</div>
	</div>

</div>
