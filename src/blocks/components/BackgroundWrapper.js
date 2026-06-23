/**
 * BackgroundWrapper — shared decorative-background wrapper for blocks.
 *
 * Like TranslatableWrapper, this is a COMPONENT you import into a block's
 * edit.js and wrap your content with — there are no InnerBlocks. It paints a
 * fixed-height decorative band (beams + gradient mesh) behind/around whatever
 * you pass as children.
 *
 *   <BackgroundWrapper blockProps={blockProps} attributes={attributes} setAttributes={setAttributes}>
 *       ...your block content...
 *   </BackgroundWrapper>
 *
 * The band is a fixed height (h-96 / 24rem). Everything decorative — beams AND
 * the gradient mesh — lives inside it. Positioning (mirrors the frontend
 * snel_background_open()):
 * - 'absolute' (default) → band is pulled out of flow at the top, content sits
 *   over it. Pure backdrop, no layout impact.
 * - 'relative' → band sits in flow, so content is pushed underneath it.
 *
 * @package Snel
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import Beams from './Beams';
import Mesh from './Mesh';

const POSITION_OPTIONS = [
	{ label: __('Absolute (behind content)', 'snel'), value: 'absolute' },
	{ label: __('Relative (pushes content down)', 'snel'), value: 'relative' },
];

const BACKDROP_OPTIONS = [
	{ label: __('White', 'snel'), value: 'white' },
	{ label: __('Dark', 'snel'), value: 'dark' },
	{ label: __('Transparent', 'snel'), value: 'transparent' },
];

const BACKDROP_CLASS = {
	white: 'bg-white',
	dark: 'bg-neutral-950',
	transparent: '',
};

export default function BackgroundWrapper({ blockProps, attributes, setAttributes, children, fade = 'from-white', showBeams = true, showGradient = true }) {
	const bgPosition = attributes?.bgPosition || 'absolute';
	const backdrop = attributes?.backdrop || 'white';

	const isRelative = bgPosition === 'relative';

	// The decorative band — fixed height; out of flow (absolute) or in flow.
	const bandClass = isRelative
		? 'pointer-events-none relative z-0 h-96 w-full overflow-hidden'
		: 'pointer-events-none absolute inset-x-0 top-0 z-0 h-96 overflow-hidden';

	const { className: propsClassName, ...restProps } = blockProps || {};

	return (
		<>
			{setAttributes && (
				<InspectorControls>
					<PanelBody title={__('Background', 'snel')} initialOpen>
						<SelectControl
							label={__('Position', 'snel')}
							value={bgPosition}
							options={POSITION_OPTIONS}
							onChange={(v) => setAttributes({ bgPosition: v })}
							__nextHasNoMarginBottom
						/>
						<SelectControl
							label={__('Backdrop', 'snel')}
							value={backdrop}
							options={BACKDROP_OPTIONS}
							onChange={(v) => setAttributes({ backdrop: v })}
							__nextHasNoMarginBottom
						/>
					</PanelBody>
				</InspectorControls>
			)}

			<div
				{...restProps}
				className={`${propsClassName || ''} relative isolate overflow-hidden ${BACKDROP_CLASS[backdrop] || ''}`}
			>
				<div className={bandClass}>
					{showBeams && <Beams />}
					{showGradient && <Mesh fade={fade} />}
				</div>
				<div className="relative z-10">{children}</div>
			</div>
		</>
	);
}
