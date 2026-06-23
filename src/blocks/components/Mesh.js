/**
 * Mesh — CSS gradient-mesh band (no grid; the beams handle that).
 *
 * Big, heavily-blurred brand-color blobs spread edge-to-edge so they melt into
 * one smooth gradient (the SMIT "lightMesh" look without WebGL), drifting
 * slowly and fading to white at the bottom. Fills its parent (the fixed-height
 * band). Frontend twin: snel_mesh() in inc/blocks/index.php — keep in sync.
 *
 * @package Snel
 */

// Brand gradient, ordered left → right: violet → sky → pink → red → teal.
// Positions run off both edges so the blend reaches the sides.
const BLOBS = [
	{ color: 'bg-violet-400/50', anim: 'animate-mesh-1', pos: 'left-[-12%] top-[-35%]' },
	{ color: 'bg-sky-400/50', anim: 'animate-mesh-2', pos: 'left-[18%] top-[-20%]' },
	{ color: 'bg-pink-400/45', anim: 'animate-mesh-3', pos: 'left-[42%] top-[-30%]' },
	{ color: 'bg-red-300/40', anim: 'animate-mesh-2', pos: 'left-[62%] top-[-18%]' },
	{ color: 'bg-teal-300/55', anim: 'animate-mesh-1', pos: 'left-[88%] top-[-32%]' },
];

// `fade` = Tailwind from-* class for the bottom fade (default white; the dark
// panel passes violet). Frontend twin: snel_mesh($fade) in inc/blocks/index.php.
export default function Mesh({ fade = 'from-white' }) {
	return (
		<div className="pointer-events-none absolute inset-0 overflow-hidden">
			{/* Big, overlapping, heavily-blurred blobs that blend into one gradient */}
			<div className="absolute inset-0 opacity-50">
				{BLOBS.map((b, i) => (
					<span
						key={i}
						className={`absolute h-[46rem] w-[46rem] rounded-full blur-[140px] ${b.color} ${b.anim} ${b.pos}`}
					/>
				))}
			</div>
			{/* Fade to the base colour so the gradient melts into it */}
			<div className={`absolute inset-0 bg-gradient-to-t ${fade}`} />
		</div>
	);
}
