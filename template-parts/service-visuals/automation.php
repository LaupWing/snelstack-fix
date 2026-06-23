<?php defined('ABSPATH') || exit; ?>
<div class="w-full max-w-md rounded-3xl border border-white/10  overflow-hidden p-6" style="background:#020617">
	<div class="flex items-center justify-between mb-5">
		<div class="text-xs font-semibold text-white/40 uppercase tracking-wider">Workflow actief</div>
		<div class="flex items-center gap-1.5 rounded-full border border-teal-300/30 bg-teal-300/10 px-3 py-1 text-xs text-teal-300">
			<span class="h-1.5 w-1.5 rounded-full bg-teal-300 animate-pulse inline-block"></span>
			Live
		</div>
	</div>
	<svg viewBox="0 0 360 200" class="w-full" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<defs>
			<marker id="arr-sky" markerWidth="6" markerHeight="6" refX="3" refY="3" orient="auto">
				<path d="M0,0 L6,3 L0,6 Z" fill="#38bdf860"/>
			</marker>
			<marker id="arr-violet" markerWidth="6" markerHeight="6" refX="3" refY="3" orient="auto">
				<path d="M0,0 L6,3 L0,6 Z" fill="#a78bfa60"/>
			</marker>
			<marker id="arr-pink" markerWidth="6" markerHeight="6" refX="3" refY="3" orient="auto">
				<path d="M0,0 L6,3 L0,6 Z" fill="#f472b660"/>
			</marker>
		</defs>
		<path d="M 100 60 C 150 60 160 100 200 100" stroke="#38bdf8" stroke-width="1.5" fill="none" stroke-opacity="0.4" stroke-dasharray="5 3" marker-end="url(#arr-sky)"/>
		<path d="M 258 100 C 290 100 295 55 318 55" stroke="#a78bfa" stroke-width="1.5" fill="none" stroke-opacity="0.4" stroke-dasharray="5 3" marker-end="url(#arr-violet)"/>
		<path d="M 258 100 C 290 100 295 145 318 145" stroke="#f472b6" stroke-width="1.5" fill="none" stroke-opacity="0.4" stroke-dasharray="5 3" marker-end="url(#arr-pink)"/>
		<circle r="3" fill="#38bdf8" opacity="0.9">
			<animateMotion dur="2.2s" repeatCount="indefinite" path="M 100 60 C 150 60 160 100 200 100"/>
		</circle>
		<circle r="3" fill="#a78bfa" opacity="0.9">
			<animateMotion dur="2.2s" repeatCount="indefinite" begin="0.6s" path="M 258 100 C 290 100 295 55 318 55"/>
		</circle>
		<circle r="3" fill="#f472b6" opacity="0.9">
			<animateMotion dur="2.2s" repeatCount="indefinite" begin="1.2s" path="M 258 100 C 290 100 295 145 318 145"/>
		</circle>
		<g>
			<rect x="8" y="36" width="92" height="48" rx="12" fill="#38bdf808" stroke="#38bdf8" stroke-width="1" stroke-opacity="0.45"/>
			<circle cx="30" cy="60" r="10" fill="#38bdf810" stroke="#38bdf8" stroke-opacity="0.5" stroke-width="1"/>
			<text x="30" y="65" text-anchor="middle" font-size="11" fill="#38bdf8">⚡</text>
			<text x="54" y="55" font-size="8.5" fill="white" opacity="0.65" font-family="sans-serif">Webhook</text>
			<text x="54" y="68" font-size="7" fill="white" opacity="0.35" font-family="sans-serif">Lead binnenkomst</text>
		</g>
		<g>
			<rect x="168" y="76" width="90" height="48" rx="12" fill="#a78bfa08" stroke="#a78bfa" stroke-width="1" stroke-opacity="0.45"/>
			<circle cx="190" cy="100" r="10" fill="#a78bfa10" stroke="#a78bfa" stroke-opacity="0.5" stroke-width="1"/>
			<text x="190" y="105" text-anchor="middle" font-size="11" fill="#a78bfa">⚙</text>
			<text x="214" y="95" font-size="8.5" fill="white" opacity="0.65" font-family="sans-serif">Verwerk</text>
			<text x="214" y="108" font-size="7" fill="white" opacity="0.35" font-family="sans-serif">AI classificatie</text>
		</g>
		<g>
			<rect x="316" y="31" width="40" height="48" rx="10" fill="#5eead408" stroke="#5eead4" stroke-width="1" stroke-opacity="0.45"/>
			<text x="336" y="52" text-anchor="middle" font-size="13" fill="#5eead4">✉</text>
			<text x="336" y="68" text-anchor="middle" font-size="7" fill="white" opacity="0.4" font-family="sans-serif">E-mail</text>
		</g>
		<g>
			<rect x="316" y="121" width="40" height="48" rx="10" fill="#f472b608" stroke="#f472b6" stroke-width="1" stroke-opacity="0.45"/>
			<text x="336" y="142" text-anchor="middle" font-size="13" fill="#f472b6">👤</text>
			<text x="336" y="158" text-anchor="middle" font-size="7" fill="white" opacity="0.4" font-family="sans-serif">CRM</text>
		</g>
	</svg>
	<div class="grid grid-cols-3 gap-2 mt-4">
		<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
			<div class="text-xl font-bold text-sky-400">847</div>
			<div class="text-[10px] text-white/35 mt-0.5">Runs</div>
		</div>
		<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
			<div class="text-xl font-bold text-teal-300">5.2u</div>
			<div class="text-[10px] text-white/35 mt-0.5">Bespaard/week</div>
		</div>
		<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center">
			<div class="text-xl font-bold text-violet-400">99%</div>
			<div class="text-[10px] text-white/35 mt-0.5">Succesrate</div>
		</div>
	</div>
</div>
