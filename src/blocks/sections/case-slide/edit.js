/**
 * Case Slide — Editor Component.
 *
 * Image via MediaUpload + label/value card fields in the sidebar. The preview
 * mirrors the front-end card styling loosely; render.php is the source of truth.
 */
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { imageId, imageUrl, label, value } = attributes;
	const blockProps = useBlockProps({ className: 'snel-case-slide-editor' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Afbeelding', 'snel')} initialOpen>
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={['image']}
							value={imageId}
							onSelect={(media) => setAttributes({
								imageId: media.id,
								imageUrl: media.sizes?.large?.url ?? media.url,
								imageAlt: media.alt ?? '',
							})}
							render={({ open }) => (
								<div>
									<Button variant="secondary" onClick={open}>
										{imageUrl ? __('Afbeelding vervangen', 'snel') : __('Afbeelding kiezen', 'snel')}
									</Button>
									{imageUrl && (
										<Button variant="tertiary" isDestructive onClick={() => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' })}>
											{__('Verwijderen', 'snel')}
										</Button>
									)}
								</div>
							)}
						/>
					</MediaUploadCheck>
				</PanelBody>
				<PanelBody title={__('Info kaart', 'snel')} initialOpen>
					<TextControl label={__('Label', 'snel')} value={label} onChange={(v) => setAttributes({ label: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Waarde', 'snel')} value={value} onChange={(v) => setAttributes({ value: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div style={{ position: 'relative', borderRadius: '12px', overflow: 'hidden', background: '#0f172a', minHeight: '120px' }}>
					{imageUrl
						? <img src={imageUrl} alt="" style={{ display: 'block', width: '100%', aspectRatio: '16/9', objectFit: 'cover' }} />
						: (
							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={['image']}
									value={imageId}
									onSelect={(media) => setAttributes({
										imageId: media.id,
										imageUrl: media.sizes?.large?.url ?? media.url,
										imageAlt: media.alt ?? '',
									})}
									render={({ open }) => (
										<div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '120px' }}>
											<Button variant="primary" onClick={open}>{__('Afbeelding kiezen', 'snel')}</Button>
										</div>
									)}
								/>
							</MediaUploadCheck>
						)}
					{label && value && (
						<div style={{ position: 'absolute', bottom: '16px', left: '16px', padding: '12px 16px', borderRadius: '12px', border: '1px solid rgba(255,255,255,.1)', background: 'rgba(0,0,0,.35)', backdropFilter: 'blur(4px)' }}>
							<span style={{ display: 'block', fontSize: '12px', color: 'rgba(255,255,255,.5)' }}>{label}</span>
							<span style={{ display: 'block', marginTop: '4px', fontSize: '18px', color: '#fff' }}>{value}</span>
						</div>
					)}
				</div>
			</div>
		</>
	);
}
