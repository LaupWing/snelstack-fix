/**
 * Case Slider — Editor Component.
 *
 * Mirrors the front-end: slides in a horizontal snap track, arrows to move
 * between them so each slide can be selected/edited in place. render.php is
 * the source of truth for the front-end shell.
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const TEMPLATE = [['snel/case-slide']];

const ChevronLeft = () => (
	<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className="size-4"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
);
const ChevronRight = () => (
	<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className="size-4"><path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
);
const arrowClass = 'flex size-8 items-center justify-center rounded-full border border-white/10 bg-black/25 backdrop-blur-sm text-white transition hover:bg-black/50 cursor-pointer';

export default function Edit({ attributes, setAttributes }) {
	const { backUrl, backLabel } = attributes;
	const wrapRef = useRef(null);
	// p-3: click the padding to select the slider container itself.
	const blockProps = useBlockProps({ className: 'snel-case-slider-editor relative p-3', ref: wrapRef });
	const innerProps = useInnerBlocksProps(
		{ className: 'snel-case-slider-editor-track flex w-full overflow-x-auto snap-x snap-mandatory rounded-xl bg-slate-900' },
		{ allowedBlocks: ['snel/case-slide'], template: TEMPLATE, orientation: 'horizontal' }
	);

	const slide = (dir) => {
		const track = wrapRef.current?.querySelector('.snel-case-slider-editor-track');
		if (track) track.scrollBy({ left: dir * track.clientWidth, behavior: 'smooth' });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Navigatie', 'snel')} initialOpen>
					<TextControl label={__('Back URL', 'snel')} value={backUrl} onChange={(v) => setAttributes({ backUrl: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Back Label', 'snel')} value={backLabel} onChange={(v) => setAttributes({ backLabel: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<div {...innerProps} />
				<div className="absolute bottom-7 right-7 z-10 flex items-center gap-1.5">
					<button type="button" className={arrowClass} onClick={() => slide(-1)} aria-label={__('Vorige slide', 'snel')}><ChevronLeft /></button>
					<button type="button" className={arrowClass} onClick={() => slide(1)} aria-label={__('Volgende slide', 'snel')}><ChevronRight /></button>
				</div>
			</div>
		</>
	);
}
