/**
 * Brand paragraph classes — shared by edit + save. Sizing/weight/colour live in
 * theme.css as PLAIN (unlayered) CSS on .snel-text / .snel-text-* (same reason
 * as the heading: unlayered beats WP's editor reset + Tailwind's @layer).
 */
const BASE = 'snel-text max-w-4xl';
const SIZES = ['sm', 'md', 'lg', 'xl'];

export const SIZE_OPTIONS = [
	{ label: 'S', value: 'sm' },
	{ label: 'M', value: 'md' },
	{ label: 'L (lead)', value: 'lg' },
	{ label: 'XL (lead)', value: 'xl' },
];

export const ALIGN_OPTIONS = [
	{ label: 'Left',   value: 'left' },
	{ label: 'Center', value: 'center' },
	{ label: 'Right',  value: 'right' },
];

export const LEADING_OPTIONS = [
	{ label: 'Tight',   value: 'tight' },
	{ label: 'Normal',  value: 'normal' },
	{ label: 'Relaxed', value: 'relaxed' },
	{ label: 'Loose',   value: 'loose' },
];

export function textClass(size = 'md', align = 'left', leading = 'normal') {
	const key = SIZES.includes(size) ? size : 'md';
	const alignClass   = align   !== 'left'   ? ` snel-ta-${align}`      : '';
	const leadingClass = leading !== 'normal'  ? ` snel-leading-${leading}` : '';
	return `${BASE} snel-text-${key}${alignClass}${leadingClass}`;
}
