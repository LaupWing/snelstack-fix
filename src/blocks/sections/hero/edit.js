import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { heading, subheading, ctaLabel, ctaUrl } = attributes;
	const blockProps = useBlockProps({
		className: 'relative overflow-hidden bg-white',
		style: { minHeight: '520px' },
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('CTA', 'snel')} initialOpen>
					<TextControl label={__('Label', 'snel')} value={ctaLabel} onChange={(v) => setAttributes({ ctaLabel: v })} __nextHasNoMarginBottom />
					<TextControl label={__('URL', 'snel')} value={ctaUrl} onChange={(v) => setAttributes({ ctaUrl: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{/* Placeholder background — canvas runs on frontend only */}
				<div className="pointer-events-none absolute inset-x-0 top-0 h-96 overflow-hidden bg-gradient-to-br from-violet-50 via-sky-50 to-pink-50 opacity-80" />

				<div className="relative z-10 px-8 pt-40 pb-20">
					<div className="mx-auto max-w-5xl flex flex-col gap-8">
						<div>
							<span className="inline-flex h-8 items-center gap-2 rounded-md border border-white/40 bg-white/80 px-2.5 text-sm font-medium shadow-sm text-slate-950">
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
						<RichText
							tagName="p"
							className="max-w-2xl text-lg text-slate-600"
							value={subheading}
							onChange={(v) => setAttributes({ subheading: v })}
							placeholder={__('Subheading…', 'snel')}
							allowedFormats={[]}
						/>
						{ctaLabel && (
							<div>
								<span className="inline-flex h-12 items-center rounded-full bg-violet-600 px-6 text-base font-semibold text-white">
									{ctaLabel}
								</span>
							</div>
						)}
					</div>
				</div>
			</div>
		</>
	);
}
