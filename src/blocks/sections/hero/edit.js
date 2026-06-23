import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';

export default function Edit({ attributes, setAttributes }) {
	const { heading, ctaPrimaryLabel, ctaPrimaryUrl, ctaSecondaryLabel, ctaSecondaryUrl } = attributes;
	const blockProps = useBlockProps({ style: { backgroundColor: '#ffffff' } });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Primary CTA', 'snel')} initialOpen>
					<TextControl label={__('Label', 'snel')} value={ctaPrimaryLabel} onChange={(v) => setAttributes({ ctaPrimaryLabel: v })} __nextHasNoMarginBottom />
					<TextControl label={__('URL', 'snel')} value={ctaPrimaryUrl} onChange={(v) => setAttributes({ ctaPrimaryUrl: v })} __nextHasNoMarginBottom />
				</PanelBody>
				<PanelBody title={__('Secondary CTA', 'snel')} initialOpen={false}>
					<TextControl label={__('Label', 'snel')} value={ctaSecondaryLabel} onChange={(v) => setAttributes({ ctaSecondaryLabel: v })} __nextHasNoMarginBottom />
					<TextControl label={__('URL', 'snel')} value={ctaSecondaryUrl} onChange={(v) => setAttributes({ ctaSecondaryUrl: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<BackgroundWrapper blockProps={blockProps} attributes={{ bgPosition: 'absolute', backdrop: 'white' }}>
				<div className="px-4 pt-40 pb-20 md:px-8 lg:pt-44">
					<PanelFrame>
						<div className="flex flex-col gap-8 xl:gap-12">
							<div>
								<span className="inline-flex h-8 items-center gap-2 rounded-md border border-slate-200 bg-white/80 px-2.5 text-sm font-medium text-slate-950 shadow-sm">
									⭐ Score 4.9 · op basis van 74 reviews
								</span>
							</div>
							<RichText
								tagName="h1"
								className="max-w-4xl font-semibold text-slate-950 text-5xl/tight"
								value={heading}
								onChange={(v) => setAttributes({ heading: v })}
								placeholder={__('Heading…', 'snel')}
								allowedFormats={['core/italic']}
							/>
							<div className="flex flex-wrap gap-4">
								<span className="inline-flex h-12 items-center gap-2 rounded-full bg-violet-600 px-6 text-base font-semibold text-white">
									{ctaPrimaryLabel}
								</span>
								<span className="inline-flex h-12 items-center gap-2 rounded-full border-2 border-violet-400/40 px-5 font-medium text-violet-600">
									{ctaSecondaryLabel}
								</span>
							</div>
						</div>
					</PanelFrame>
				</div>
			</BackgroundWrapper>
		</>
	);
}
