<?php defined('ABSPATH') || exit; ?>
<style>
@keyframes snel-terminal-in {
	from { opacity: 0; transform: translateY(4px); }
	to   { opacity: 1; transform: translateY(0); }
}
@keyframes snel-error-flash {
	0%, 100% { border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.08); }
	50%       { border-color: rgba(239,68,68,0.6); background: rgba(239,68,68,0.14); }
}
@keyframes snel-fix-glow {
	0%, 100% { border-color: rgba(94,234,212,0.25); }
	50%       { border-color: rgba(94,234,212,0.5); }
}
.snel-t-in      { animation: snel-terminal-in 0.35s ease both; }
.snel-err-flash { animation: snel-error-flash 2s ease-in-out 0.5s infinite; }
.snel-fix-glow  { animation: snel-fix-glow 2s ease-in-out 1.2s infinite; }
</style>
<div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/[0.03] overflow-hidden" style="background:#020617">
	<div class="flex items-center gap-2 px-4 py-3 border-b border-white/10 bg-white/[0.04]">
		<span class="h-2.5 w-2.5 rounded-full bg-red-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-yellow-400/50"></span>
		<span class="h-2.5 w-2.5 rounded-full bg-green-400/50"></span>
		<div class="ml-2 flex-1 rounded-md bg-white/5 border border-white/10 px-3 py-1 text-xs text-white/25 font-mono">terminal</div>
	</div>
	<div class="p-5 space-y-3 font-mono text-xs">
		<div class="snel-t-in snel-err-flash flex gap-2 items-start rounded-lg border px-3 py-2" style="animation-delay:0.05s">
			<span class="text-red-400 shrink-0 mt-0.5">!</span>
			<div>
				<div class="text-red-400 font-semibold mb-1">TypeError: Cannot read properties</div>
				<div class="text-white/40">of undefined (reading 'map')</div>
				<div class="text-white/25 mt-1">at Dashboard.jsx:47</div>
			</div>
		</div>
		<div class="snel-t-in flex gap-2 items-center" style="animation-delay:0.2s">
			<span class="text-white/30 shrink-0">$</span>
			<span class="text-teal-300">snel fix</span>
			<span class="text-white/40">--analyze</span>
			<span class="inline-block w-0.5 h-3.5 bg-teal-300 ml-1 align-middle animate-pulse"></span>
		</div>
		<div class="snel-t-in flex gap-2" style="animation-delay:0.35s">
			<span class="text-white/30 shrink-0">~</span>
			<span class="text-white/50">Analyseren...</span>
		</div>
		<div class="snel-t-in snel-fix-glow flex gap-2 items-start rounded-lg border px-3 py-2" style="animation-delay:0.5s">
			<span class="text-teal-300 shrink-0 mt-0.5">+</span>
			<div>
				<div class="text-teal-300 font-semibold mb-1">Oorzaak gevonden</div>
				<div class="text-white/50">API response is <span class="text-amber-300">null</span> bij lege dataset</div>
				<div class="text-white/35 mt-1">Oplossing: optional chaining + fallback</div>
			</div>
		</div>
		<div class="snel-t-in flex gap-2 items-center" style="animation-delay:0.65s">
			<span class="text-white/30 shrink-0">~</span>
			<span class="text-white/40">Patch toegepast op 3 bestanden</span>
		</div>
		<div class="snel-t-in flex gap-2 items-center" style="animation-delay:0.8s">
			<span class="text-green-400 shrink-0">✓</span>
			<span class="text-green-400 font-semibold">Build geslaagd</span>
			<span class="text-white/30 ml-auto">2.3s</span>
		</div>
	</div>
	<div class="border-t border-white/10 px-5 py-3 flex items-center justify-between">
		<div class="flex items-center gap-2">
			<span class="h-2 w-2 rounded-full bg-green-400 animate-pulse inline-block"></span>
			<span class="text-xs text-white/50 font-mono">Probleem opgelost</span>
		</div>
		<span class="text-xs text-white/30 font-mono">gemiddeld &lt; 24u</span>
	</div>
</div>
