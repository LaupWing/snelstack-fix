/**
 * Snel Intro — Editor.
 *
 * Left: 4 locked slots (eyebrow / heading / body / cta).
 * Right: visual partial chosen from inspector dropdown (rendered server-side).
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';

const TEMPLATE = [
	['snel/slot', { className: 'snel-slot-eyebrow', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-heading', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-body', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-cta', max: 2, orientation: 'horizontal', justify: 'left' }],
];

const VISUAL_OPTIONS = [
	{ label: '— None —',          value: '' },
	{ label: 'Speed & First Impression', value: 'speed' },
	{ label: 'Website',           value: 'website' },
	{ label: 'AI',                value: 'ai' },
	{ label: 'Automation',        value: 'automation' },
	{ label: 'SEO',               value: 'seo' },
	{ label: 'Retainer',          value: 'retainer' },
];

export default function Edit({ attributes, setAttributes }) {
	const { fullHeight, visual, showBeams, showGradient } = attributes;
	const blockProps = useBlockProps({ className: `snel-hero${fullHeight ? ' min-h-screen' : ''}`, style: { backgroundColor: '#ffffff' } });
	const innerProps = useInnerBlocksProps(
		{ className: 'snel-intro-slots' },
		{ template: TEMPLATE, templateLock: 'all' }
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Layout', 'snel')} initialOpen>
					<ToggleControl
						label={__('Full height (min-h-screen)', 'snel')}
						checked={fullHeight}
						onChange={(v) => setAttributes({ fullHeight: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Right visual', 'snel')}
						value={visual}
						options={VISUAL_OPTIONS}
						onChange={(v) => setAttributes({ visual: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				<PanelBody title={__('Background', 'snel')} initialOpen={false}>
					<ToggleControl
						label={__('Beams', 'snel')}
						checked={showBeams}
						onChange={(v) => setAttributes({ showBeams: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Gradient', 'snel')}
						checked={showGradient}
						onChange={(v) => setAttributes({ showGradient: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<BackgroundWrapper blockProps={blockProps} attributes={{ bgPosition: 'absolute', backdrop: 'transparent' }} showBeams={showBeams} showGradient={showGradient}>
				<div className="px-4 pt-40 pb-20 md:px-8 lg:pt-44">
					<PanelFrame>
						<div className="grid lg:grid-cols-2 gap-16 xl:gap-32 items-center">
							<div {...innerProps} />
							<div className="relative flex items-center justify-center min-h-64">
								{visual
									? <span className="text-slate-400 text-sm font-mono">{visual}</span>
									: <span className="text-slate-500 text-sm">Pick a visual →</span>
								}
							</div>
						</div>
					</PanelFrame>
				</div>
			</BackgroundWrapper>
		</>
	);
}
