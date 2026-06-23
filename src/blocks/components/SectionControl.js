import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

// ─── Background ──────────────────────────────────────────────────────────────

export const BG_OPTIONS = [
	{ label: 'Dark (violet)', value: 'dark' },
	{ label: 'Canvas (black)', value: 'canvas' },
	{ label: 'White',          value: 'white' },
];

const BG_COLORS = {
	dark:   '#2e1065',
	canvas: '#020617',
	white:  '#ffffff',
};

export function getSectionStyle(value) {
	return { backgroundColor: BG_COLORS[value] ?? BG_COLORS.white };
}

export function getSectionClass(value) {
	if (value === 'dark')   return 'is-dark';
	if (value === 'canvas') return 'is-dark bg-canvas';
	return 'bg-white';
}

// ─── Padding ─────────────────────────────────────────────────────────────────

export const SIZE_OPTIONS = [
	{ label: 'Small',  value: 'sm' },
	{ label: 'Medium', value: 'md' },
	{ label: 'Large',  value: 'lg' },
];

export function getSectionPaddingClass(size = 'md', disableTop = false, disableBottom = false) {
	const top    = { sm: 'pt-12 lg:pt-16', md: 'pt-20 lg:pt-28', lg: 'pt-24 lg:pt-32' };
	const bottom = { sm: 'pb-12 lg:pb-16', md: 'pb-20 lg:pb-28', lg: 'pb-24 lg:pb-32' };
	const parts  = [];
	if (!disableTop)    parts.push(top[size]    ?? top.md);
	if (!disableBottom) parts.push(bottom[size] ?? bottom.md);
	return parts.join(' ');
}

// ─── Component ───────────────────────────────────────────────────────────────

export default function SectionControl({
	value, onChange,
	size, onSizeChange,
	disableTop, onDisableTopChange,
	disableBottom, onDisableBottomChange,
}) {
	return (
		<PanelBody title={__('Section', 'snel')} initialOpen>
			<SelectControl
				label={__('Background', 'snel')}
				value={value}
				options={BG_OPTIONS}
				onChange={onChange}
				__nextHasNoMarginBottom
			/>
			{onSizeChange && <>
				<SelectControl
					label={__('Padding size', 'snel')}
					value={size}
					options={SIZE_OPTIONS}
					onChange={onSizeChange}
					__nextHasNoMarginBottom
				/>
				<ToggleControl
					label={__('Remove top padding', 'snel')}
					checked={!!disableTop}
					onChange={onDisableTopChange}
					__nextHasNoMarginBottom
				/>
				<ToggleControl
					label={__('Remove bottom padding', 'snel')}
					checked={!!disableBottom}
					onChange={onDisableBottomChange}
					__nextHasNoMarginBottom
				/>
			</>}
		</PanelBody>
	);
}
