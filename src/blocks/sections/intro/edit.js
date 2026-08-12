/**
 * Snel Intro — Editor.
 *
 * Left: 4 locked slots (eyebrow / heading / body / cta).
 * Right: visual partial chosen from inspector dropdown (rendered server-side).
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { SelectControl, ToggleControl, Button, BaseControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';
import SectionControl, { getSectionStyle, getSectionClass } from '../../components/SectionControl';

const TEMPLATE = [
	['snel/slot', { className: 'snel-slot-eyebrow', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-heading', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-body', max: 1, orientation: 'vertical' }],
	['snel/slot', { className: 'snel-slot-cta', max: 2, orientation: 'horizontal', justify: 'left' }],
];

// Keep in sync with $image_ratio in render.php.
const RATIO_CLASSES = {
	original:  '',
	square:    'object-cover aspect-square',
	landscape: 'object-cover aspect-[4/3]',
	portrait:  'object-cover aspect-[3/4]',
	wide:      'object-cover aspect-video',
};

const RATIO_OPTIONS = [
	{ label: 'Original',        value: 'original' },
	{ label: 'Square — 1:1',    value: 'square' },
	{ label: 'Landscape — 4:3', value: 'landscape' },
	{ label: 'Portrait — 3:4',  value: 'portrait' },
	{ label: 'Wide — 16:9',     value: 'wide' },
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
	const { fullHeight, visual, imageId, imageUrl, imageRatio, theme, showBeams, showGradient } = attributes;
	const imageClass = `w-full rounded-2xl ${RATIO_CLASSES[imageRatio] ?? ''}`.trim();
	const isDark = theme === 'dark' || theme === 'canvas';
	const fade   = theme === 'canvas' ? 'from-[#020617]' : theme === 'dark' ? 'from-[#2e1065]' : 'from-white';
	const blockProps = useBlockProps({
		className: `snel-hero${fullHeight ? ' min-h-screen' : ''} ${getSectionClass(theme)}`,
		style: getSectionStyle(theme),
	});
	const innerProps = useInnerBlocksProps(
		{ className: 'snel-intro-slots' },
		{ template: TEMPLATE, templateLock: 'all' }
	);

	return (
		<>
			<InspectorControls>
				<SectionControl
					value={theme} onChange={(v) => setAttributes({ theme: v })}
					showBeams={showBeams} onShowBeamsChange={(v) => setAttributes({ showBeams: v })}
					showGradient={showGradient} onShowGradientChange={(v) => setAttributes({ showGradient: v })}
				>
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
					<BaseControl label={__('Right image (overrides visual)', 'snel')} __nextHasNoMarginBottom>
						<MediaUploadCheck>
							<MediaUpload
								allowedTypes={['image']}
								value={imageId}
								onSelect={(media) => setAttributes({ imageId: media.id, imageUrl: media.sizes?.large?.url ?? media.url })}
								render={({ open }) => (
									<div>
										{imageUrl && <img src={imageUrl} alt="" style={{ width: '100%', borderRadius: '8px', marginBottom: '8px' }} />}
										<Button variant="secondary" onClick={open}>
											{imageUrl ? __('Replace image', 'snel') : __('Choose image', 'snel')}
										</Button>
										{imageUrl && (
											<Button variant="tertiary" isDestructive onClick={() => setAttributes({ imageId: 0, imageUrl: '' })}>
												{__('Remove', 'snel')}
											</Button>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
					</BaseControl>
					{imageUrl && (
						<SelectControl
							label={__('Image ratio', 'snel')}
							value={imageRatio}
							options={RATIO_OPTIONS}
							onChange={(v) => setAttributes({ imageRatio: v })}
							__nextHasNoMarginBottom
						/>
					)}
				</SectionControl>
			</InspectorControls>
			<BackgroundWrapper blockProps={blockProps} attributes={{ bgPosition: 'absolute', backdrop: 'transparent' }} fade={fade} showBeams={showBeams} showGradient={showGradient}>
				<div className="px-4 pt-40 pb-20 md:px-8 lg:pt-44">
					<PanelFrame dark={isDark}>
						<div className="grid lg:grid-cols-2 gap-16 xl:gap-32 items-center">
							<div {...innerProps} />
							<div className="relative flex items-center justify-center min-h-64">
								{imageUrl
									? <img src={imageUrl} alt="" className={imageClass} />
									: visual
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
