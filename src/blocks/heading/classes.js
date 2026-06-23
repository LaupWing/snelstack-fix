/**
 * Brand heading classes — one source of truth for edit + save so the editor and
 * front end render identical markup.
 *
 * `level` = the HTML tag (h1–h4, semantics/SEO). `size` = the visual size,
 * independent of the tag. size 'auto' uses the level's default.
 *
 * Sizing/weight/colour live in theme.css as PLAIN (unlayered) CSS on
 * .snel-heading / .snel-h-* — NOT Tailwind utilities — because Tailwind v4 puts
 * utilities in @layer, and WP's editor heading reset is unlayered and would beat
 * a layered utility. Unlayered wins, so the heading sizes hold in the editor.
 */
const BASE = 'snel-heading max-w-4xl';

// Default size key per level (used when size = 'auto').
const LEVEL_DEFAULT = { h1: 'xl', h2: 'lg', h3: 'md', h4: 'sm' };

export const SIZE_OPTIONS = [
	{ label: 'Auto (by level)', value: 'auto' },
	{ label: 'S',   value: 'sm' },
	{ label: 'M',   value: 'md' },
	{ label: 'L',   value: 'lg' },
	{ label: 'XL',  value: 'xl' },
	{ label: '2XL', value: '2xl' },
	{ label: '3XL', value: '3xl' },
	{ label: '4XL', value: '4xl' },
	{ label: '5XL', value: '5xl' },
];

export const WEIGHT_OPTIONS = [
	{ label: 'Regular',    value: 'regular' },
	{ label: 'Bold',       value: 'bold' },
	{ label: 'Extra Bold', value: 'extrabold' },
];

export const ALIGN_OPTIONS = [
	{ label: 'Left',   value: 'left' },
	{ label: 'Center', value: 'center' },
	{ label: 'Right',  value: 'right' },
];

export function headingClass(level, size = 'auto', weight = 'regular', align = 'left', gradient = false) {
	const key = size !== 'auto' ? size : LEVEL_DEFAULT[level] || 'lg';
	const weightClass   = weight   !== 'regular' ? ` snel-hw-${weight}`  : '';
	const alignClass    = align    !== 'left'     ? ` snel-ta-${align}`   : '';
	const gradientClass = gradient               ? ' snel-hg-animated'   : '';
	return `${BASE} snel-h-${key}${weightClass}${alignClass}${gradientClass}`;
}
