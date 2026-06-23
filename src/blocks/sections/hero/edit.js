import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { heading, subheading, imageUrl, ctaLabel, ctaUrl } = attributes;
	const blockProps = useBlockProps({ className: 'bg-white' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('CTA', 'snel')} initialOpen>
					<TextControl label={__('Label', 'snel')} value={ctaLabel} onChange={(v) => setAttributes({ ctaLabel: v })} __nextHasNoMarginBottom />
					<TextControl label={__('URL', 'snel')} value={ctaUrl} onChange={(v) => setAttributes({ ctaUrl: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div className="mx-auto max-w-5xl px-6 py-32 flex flex-col gap-6 md:flex-row md:items-center md:gap-12">
					<div className="flex flex-col gap-6 md:w-1/2">
						<RichText
							tagName="h1"
							className="text-4xl font-bold text-slate-900 lg:text-5xl"
							value={heading}
							onChange={(v) => setAttributes({ heading: v })}
							placeholder={__('Heading…', 'snel')}
						/>
						<RichText
							tagName="p"
							className="text-lg text-slate-600"
							value={subheading}
							onChange={(v) => setAttributes({ subheading: v })}
							placeholder={__('Subheading…', 'snel')}
						/>
						<div>
							<span className="inline-flex h-11 items-center rounded-full bg-violet-600 px-6 font-semibold text-white">
								{ctaLabel || __('CTA', 'snel')}
							</span>
						</div>
					</div>
					<div className="md:w-1/2">
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => setAttributes({ imageUrl: media.url })}
								allowedTypes={['image']}
								render={({ open }) =>
									imageUrl
										? <img src={imageUrl} className="w-full rounded-2xl object-cover" onClick={open} style={{ cursor: 'pointer' }} />
										: <Button onClick={open} className="w-full h-48 border-2 border-dashed border-slate-300 rounded-2xl text-slate-400">
											{__('+ Add image', 'snel')}
										</Button>
								}
							/>
						</MediaUploadCheck>
					</div>
				</div>
			</div>
		</>
	);
}
