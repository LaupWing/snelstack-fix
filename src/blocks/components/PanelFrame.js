/**
 * PanelFrame — editor twin of snel_panel_open()/close() (inc/blocks/panel-frame.php).
 *
 * The shared framed card: max-w-5xl, hairline gradient borders, and four animated
 * brand "stack" corners. Wrap your content (usually the InnerBlocks div) with it:
 *
 *   <PanelFrame dark={isDark}>
 *       <div {...innerBlocksProps} />
 *   </PanelFrame>
 *
 * Keep this in sync with snel_panel_open() so editor === front end.
 *
 * @package Snel
 */

// Brand "stack" corner icon — three isometric plates, staggered so one corner
// waves at a time (matches snel_stack_icon).
const PALETTE = ['#5eead4', '#38bdf8', '#a78bfa', '#f472b6', '#fca5a5'];
const YS = [14, 9.5, 5];

const StackIcon = ({ corner }) => {
	const cols = [0, 1, 2].map((n) => PALETTE[(corner + n) % PALETTE.length]);
	return (
		<svg viewBox="0 0 24 24" className="size-full overflow-visible" xmlns="http://www.w3.org/2000/svg">
			{cols.map((c, i) => (
				<g key={i} className="snel-stack-layer" style={{ animationDelay: `${-(corner * 1500 + i * 6000)}ms` }}>
					<rect x="-7" y="-7" width="14" height="14" rx="5" transform={`translate(12 ${YS[i]}) scale(1 0.62) rotate(45)`} fill={c} />
				</g>
			))}
		</svg>
	);
};

// pos → stagger index, matching snel_panel_open().
const CORNERS = [
	['-top-2.5 -left-2.5', 0],
	['-top-2.5 -right-2.5', 1],
	['-bottom-2.5 -left-2.5', 3],
	['-bottom-2.5 -right-2.5', 2],
];

export default function PanelFrame({ dark = false, max = 'max-w-5xl', children }) {
	// Borders: sky/violet on light; hairline white on dark.
	const border = dark ? 'via-white/10' : 'via-sky-400/70';
	const borderAlt = dark ? 'via-white/10' : 'via-violet-500/70';

	return (
		<div className={`relative mx-auto w-full ${max} p-8 xl:p-14`}>
			{/* Hairline borders. */}
			<div className={`pointer-events-none absolute left-4 right-4 top-0 h-px bg-gradient-to-r from-transparent ${border} to-transparent`}></div>
			<div className={`pointer-events-none absolute left-4 right-4 bottom-0 h-px bg-gradient-to-r from-transparent ${borderAlt} to-transparent`}></div>
			<div className={`pointer-events-none absolute top-4 bottom-4 left-0 w-px bg-gradient-to-b from-transparent ${border} to-transparent`}></div>
			<div className={`pointer-events-none absolute top-4 bottom-4 right-0 w-px bg-gradient-to-b from-transparent ${borderAlt} to-transparent`}></div>

			{/* Corner stacks. */}
			{CORNERS.map(([pos, corner]) => (
				<div key={pos} className={`absolute ${pos} size-5`}>
					<StackIcon corner={corner} />
				</div>
			))}

			{children}
		</div>
	);
}
