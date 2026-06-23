/**
 * Beams — animated SVG background (the Aceternity "Background Beams" effect).
 *
 * 50 diagonal beam paths, each stroked with a linearGradient that sweeps
 * cyan → purple → magenta. The sweep is animated with NATIVE SVG <animate>
 * (SMIL) — no framer-motion, no JS dependency, no view script. The exact same
 * markup is produced on the frontend by snel_beams_svg() in
 * inc/blocks/background.php — keep the two in sync.
 *
 * @package Snel
 */
import { useInstanceId } from '@wordpress/compose';

const COUNT = 50;       // animated beams
const BASE_COUNT = 60;  // faint static base bundle

// One beam path. The whole family is a parametric translation: every point
// shifts +7 on x and -8 on y per index, so we generate instead of hardcoding.
function buildPath(i) {
	const dx = 7 * i;
	const dy = -8 * i;
	const p = (x, y) => `${x + dx} ${y + dy}`;
	return `M${p(-380, -189)}C${p(-380, -189)} ${p(-312, 216)} ${p(152, 343)}C${p(616, 470)} ${p(684, 875)} ${p(684, 875)}`;
}

export default function Beams() {
	const uid = useInstanceId(Beams, 'snel-beam');

	const beams = Array.from({ length: COUNT }, (_, i) => i);
	const basePath = Array.from({ length: BASE_COUNT }, (_, i) => buildPath(i)).join('');

	return (
		<svg
			className="pointer-events-none absolute inset-0 h-full w-full"
			width="100%"
			height="100%"
			viewBox="0 0 696 316"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
		>
			{/* Static line bundle underneath — center-bright (see radial below) */}
			<path d={basePath} stroke={`url(#${uid}-base)`} strokeOpacity="0.2" strokeWidth="0.5" />

			{/* Animated beams */}
			{beams.map((i) => (
				<path
					key={i}
					d={buildPath(i)}
					stroke={`url(#${uid}-${i})`}
					strokeOpacity="0.8"
					strokeWidth="1.5"
				/>
			))}

			<defs>
				{beams.map((i) => {
					const dur = 10 + ((i * 7) % 11); // 10–20s
					const begin = `-${(i * 3) % 10}s`; // negative → desynced at load
					const y2End = 93 + (i % 8); // 93–100%
					return (
						<linearGradient key={i} id={`${uid}-${i}`} x1="0%" y1="0%" x2="0%" y2="0%">
							<animate attributeName="x1" values="0%;100%" dur={`${dur}s`} begin={begin} repeatCount="indefinite" />
							<animate attributeName="x2" values="0%;95%" dur={`${dur}s`} begin={begin} repeatCount="indefinite" />
							<animate attributeName="y1" values="0%;100%" dur={`${dur}s`} begin={begin} repeatCount="indefinite" />
							<animate attributeName="y2" values={`0%;${y2End}%`} dur={`${dur}s`} begin={begin} repeatCount="indefinite" />
							<stop offset="0%" stopColor="#38bdf8" stopOpacity="0" />
							<stop offset="10%" stopColor="#38bdf8" />
							<stop offset="50%" stopColor="#a78bfa" />
							<stop offset="90%" stopColor="#f472b6" />
							<stop offset="100%" stopColor="#f472b6" stopOpacity="0" />
						</linearGradient>
					);
				})}

				<radialGradient
					id={`${uid}-base`}
					cx="0"
					cy="0"
					r="1"
					gradientUnits="userSpaceOnUse"
					gradientTransform="translate(352 34) rotate(90) scale(555 1560.62)"
				>
					<stop offset="0.0666667" stopColor="#64748b" />
					<stop offset="0.243243" stopColor="#64748b" />
					<stop offset="0.43594" stopColor="white" stopOpacity="0" />
				</radialGradient>
			</defs>
		</svg>
	);
}
