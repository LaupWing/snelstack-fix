/**
 * FeatureCard — editor preview component (not a block).
 * Mirrors the render.php card visually inside the block editor.
 */
import { ICONS } from './icons';

function IconSvg({ icon }) {
	const def = ICONS[icon] || ICONS.eye;
	return (
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
			strokeWidth="1" stroke="currentColor" style={{ width: 32, height: 32 }}>
			{def.paths.map((d, i) => (
				<path key={i} strokeLinecap="round" strokeLinejoin="round" d={d} />
			))}
		</svg>
	);
}

export default function FeatureCard({ icon = 'eye', heading = '', body = '' }) {
	return (
		<div style={{
			position: 'relative', borderRadius: 8, padding: '24px',
			background: 'linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01))',
			boxShadow: 'inset 0 0 0 1px rgba(255,255,255,0.05)',
			minHeight: 220, display: 'flex', flexDirection: 'column',
		}}>
			{/* Stack corner placeholder */}
			<div style={{ position: 'absolute', top: -10, right: -10, width: 20, height: 20 }}>
				<svg viewBox="0 0 24 24" style={{ width: '100%', height: '100%', overflow: 'visible' }}>
					<rect x="-7" y="-7" width="14" height="14" rx="5" transform="translate(12 14) scale(1 0.62) rotate(45)" fill="#5eead4" opacity="0.7"/>
					<rect x="-7" y="-7" width="14" height="14" rx="5" transform="translate(12 9.5) scale(1 0.62) rotate(45)" fill="#38bdf8" opacity="0.7"/>
					<rect x="-7" y="-7" width="14" height="14" rx="5" transform="translate(12 5) scale(1 0.62) rotate(45)" fill="#a78bfa" opacity="0.7"/>
				</svg>
			</div>

			<span style={{ color: '#5eead4', marginBottom: 16, display: 'flex' }}>
				<IconSvg icon={icon} />
			</span>

			<div style={{ position: 'relative', marginBottom: 80, flexShrink: 0 }}>
				<div style={{ position: 'absolute', left: 0, right: 16, bottom: 0, height: 1, background: 'linear-gradient(to right, rgba(255,255,255,0.1), transparent)' }} />
			</div>

			{heading && (
				<span style={{ display: 'block', color: '#fff', fontSize: 20, fontWeight: 600, lineHeight: 1.35, marginBottom: 12 }}
					dangerouslySetInnerHTML={{ __html: heading }} />
			)}
			{body && (
				<p style={{ color: 'rgba(255,255,255,0.7)', fontSize: 14, margin: 0, lineHeight: 1.6 }}
					dangerouslySetInnerHTML={{ __html: body }} />
			)}
		</div>
	);
}
