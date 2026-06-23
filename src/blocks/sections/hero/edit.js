/**
 * Snel Hero — Editor.
 *
 * Beams via BackgroundWrapper + the shared framed card via PanelFrame, wrapping a
 * LOCKED 3-slot layout: eyebrow / middle / lower. Slots can't be moved; each
 * accepts blocks (capped 1 / 1 / 2). PanelFrame mirrors snel_panel_open().
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';
import { getSectionPaddingClass, SIZE_OPTIONS } from '../../components/SectionControl';

// 3 fixed slots (snel/slot) — outer-locked. Each caps its own count:
//   eyebrow → 1 · middle → 1 · lower → 1–2 (side by side, left).
const TEMPLATE = [
	['snel/slot', { className: 'snel-slot-eyebrow', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-middle', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-lower', max: 2, orientation: 'horizontal', justify: 'left' }],
];

const JUSTIFY_OPTIONS = [
	{ label: 'Start',  value: 'start' },
	{ label: 'Center', value: 'center' },
	{ label: 'End',    value: 'end' },
];

const CONTENT_WIDTH_OPTIONS = [
	{ label: 'None (full)',  value: 'none' },
	{ label: 'XS — 20rem',  value: 'xs' },
	{ label: 'SM — 24rem',  value: 'sm' },
	{ label: 'MD — 28rem',  value: 'md' },
	{ label: 'LG — 32rem',  value: 'lg' },
	{ label: 'XL — 36rem',  value: 'xl' },
	{ label: '2XL — 42rem', value: '2xl' },
	{ label: '3XL — 48rem', value: '3xl' },
];

export default function Edit({ attributes, setAttributes }) {
	const { justify, contentWidth, fullHeight, size, disableBottom } = attributes;
	const cwClass = contentWidth !== 'none' ? ` snel-cw-${contentWidth}` : '';
	const blockProps = useBlockProps({ className: `snel-hero${fullHeight ? ' min-h-screen' : ''}`, style: { backgroundColor: '#ffffff' } });
	const innerProps = useInnerBlocksProps(
		{ className: `relative z-10 snel-hero-slots is-layout-flex gap-8 xl:gap-12 snel-justify-${justify}${cwClass}` },
		{ template: TEMPLATE, templateLock: 'all' }
	);
	const paddingClass = fullHeight
		? 'px-4 md:px-8'
		: `px-4 md:px-8 pt-40 lg:pt-44 ${getSectionPaddingClass(size, true, disableBottom)}`;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Layout', 'snel')} initialOpen>
					<SelectControl
						label={__('Alignment', 'snel')}
						value={justify}
						options={JUSTIFY_OPTIONS}
						onChange={(v) => setAttributes({ justify: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Content width', 'snel')}
						value={contentWidth}
						options={CONTENT_WIDTH_OPTIONS}
						onChange={(v) => setAttributes({ contentWidth: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Full height (min-h-screen)', 'snel')}
						checked={fullHeight}
						onChange={(v) => setAttributes({ fullHeight: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Bottom padding size', 'snel')}
						value={size}
						options={SIZE_OPTIONS}
						onChange={(v) => setAttributes({ size: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Remove bottom padding', 'snel')}
						checked={!!disableBottom}
						onChange={(v) => setAttributes({ disableBottom: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<BackgroundWrapper blockProps={blockProps} attributes={{ bgPosition: 'absolute', backdrop: 'transparent' }}>
				<div className={paddingClass}>
					<PanelFrame>
						<div {...innerProps} />
					</PanelFrame>
				</div>
			</BackgroundWrapper>
		</>
	);
}
