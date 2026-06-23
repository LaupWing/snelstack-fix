/**
 * Panel — Editor Component.
 *
 * Beams via BackgroundWrapper + the shared framed card via PanelFrame (same as
 * the hero), wrapping a LOCKED 3-slot layout: eyebrow / middle / lower. Adds a
 * light/dark theme toggle; on dark, PanelFrame's borders flip to white.
 * Keep in sync with render.php (snel_background_open + snel_panel_open).
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';
import SectionControl, { getSectionStyle, getSectionClass, getSectionPaddingClass } from '../../components/SectionControl';

// Same 3 fixed slots as the hero: eyebrow (1) / middle (1) / lower (1–2).
const TEMPLATE = [
	['snel/slot', { className: 'snel-slot-eyebrow', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-middle', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-lower', max: 2, orientation: 'horizontal', justify: 'left' }],
];

export default function Edit({ attributes, setAttributes }) {
	const { theme, rounded, justify, contentWidth, size } = attributes;
	const isDark   = theme === 'dark' || theme === 'canvas';
	const panelBg  = theme === 'canvas' ? '#020617' : '#2e1065';
	const fade     = theme === 'canvas' ? 'from-[#020617]' : isDark ? 'from-[#2e1065]' : 'from-white';

	const sectionStyle = rounded
		? { backgroundColor: isDark ? '#ffffff' : panelBg }
		: getSectionStyle(theme);
	const sectionClass = rounded
		? `snel-panel ${isDark ? 'bg-white' : ''}`
		: `snel-panel ${getSectionClass(theme)}`;

	const innerWrapClass = rounded
		? `rounded-t-2xl overflow-hidden ${isDark ? 'is-dark' : 'bg-white'}`
		: null;
	const innerWrapStyle = rounded
		? { backgroundColor: isDark ? panelBg : '#ffffff' }
		: null;

	const blockProps = useBlockProps({
		className: sectionClass.trim(),
		style: sectionStyle,
	});

	const cwClass = contentWidth !== 'none' ? ` snel-cw-${contentWidth}` : '';
	const innerProps = useInnerBlocksProps(
		{ className: `relative z-10 snel-panel-slots is-layout-flex gap-8 xl:gap-12 snel-justify-${justify}${cwClass}` },
		{ template: TEMPLATE, templateLock: 'all' }
	);

	const content = (
		<div className={`px-4 md:px-8 ${getSectionPaddingClass(size)}`}>
			<PanelFrame dark={isDark}>
				<div {...innerProps} />
			</PanelFrame>
		</div>
	);

	return (
		<>
			<InspectorControls>
				<SectionControl
					value={theme} onChange={(v) => setAttributes({ theme: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
				/>
				<PanelBody title={__('Layout', 'snel')} initialOpen={false}>
					<SelectControl
						label={__('Alignment', 'snel')}
						value={justify}
						options={[
							{ label: 'Start',  value: 'start' },
							{ label: 'Center', value: 'center' },
							{ label: 'End',    value: 'end' },
						]}
						onChange={(v) => setAttributes({ justify: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Content width', 'snel')}
						value={contentWidth}
						options={[
							{ label: 'None (full)',  value: 'none' },
							{ label: 'XS — 20rem',  value: 'xs' },
							{ label: 'SM — 24rem',  value: 'sm' },
							{ label: 'MD — 28rem',  value: 'md' },
							{ label: 'LG — 32rem',  value: 'lg' },
							{ label: 'XL — 36rem',  value: 'xl' },
							{ label: '2XL — 42rem', value: '2xl' },
							{ label: '3XL — 48rem', value: '3xl' },
						]}
						onChange={(v) => setAttributes({ contentWidth: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Rounded corners', 'snel')}
						checked={rounded}
						onChange={(v) => setAttributes({ rounded: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<BackgroundWrapper blockProps={blockProps} attributes={{ bgPosition: 'absolute', backdrop: 'transparent' }} fade={fade}>
				{rounded
					? <div className={innerWrapClass} style={innerWrapStyle}>{content}</div>
					: content
				}
			</BackgroundWrapper>
		</>
	);
}
