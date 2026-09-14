/**
 * Case Slide — Editor Component.
 *
 * Image via MediaUpload + label/value card fields in the sidebar. The preview
 * mirrors the front-end card styling loosely; render.php is the source of truth.
 */
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes, clientId }) {
	const { imageId, imageUrl, mobileImageId, mobileImageUrl, label, value } = attributes;
	const blockProps = useBlockProps({ className: 'snel-case-slide-editor' });

	// Volgorde: positie binnen de slider + verplaatsen zonder list view.
	const { rootClientId, index, count } = useSelect((select) => {
		const { getBlockRootClientId, getBlockIndex, getBlockCount } = select(blockEditorStore);
		const root = getBlockRootClientId(clientId);
		return {
			rootClientId: root,
			index: getBlockIndex(clientId),
			count: getBlockCount(root),
		};
	}, [clientId]);
	const { moveBlocksUp, moveBlocksDown } = useDispatch(blockEditorStore);

	const moverBtn = {
		display: 'flex', alignItems: 'center', justifyContent: 'center',
		width: '28px', height: '28px', borderRadius: '9999px',
		border: '1px solid rgba(255,255,255,.2)', background: 'rgba(0,0,0,.55)',
		color: '#fff', cursor: 'pointer', fontSize: '14px', lineHeight: 1,
	};

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
				<PanelBody title={__('Mobiele afbeelding (optioneel)', 'snel')} initialOpen={false}>
					<p style={{ fontSize: '12px', color: '#757575', marginTop: 0 }}>
						{__('Getoond onder 768px. Leeg = desktop-afbeelding overal.', 'snel')}
					</p>
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={['image']}
							value={mobileImageId}
							onSelect={(media) => setAttributes({
								mobileImageId: media.id,
								mobileImageUrl: media.sizes?.large?.url ?? media.url,
							})}
							render={({ open }) => (
								<div>
									{mobileImageUrl && (
										<img src={mobileImageUrl} alt="" style={{ display: 'block', width: '100%', borderRadius: '8px', marginBottom: '8px', aspectRatio: '4/5', objectFit: 'cover' }} />
									)}
									<Button variant="secondary" onClick={open}>
										{mobileImageUrl ? __('Afbeelding vervangen', 'snel') : __('Afbeelding kiezen', 'snel')}
									</Button>
									{mobileImageUrl && (
										<Button variant="tertiary" isDestructive onClick={() => setAttributes({ mobileImageId: 0, mobileImageUrl: '' })}>
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
					<div style={{ position: 'absolute', top: '12px', right: '12px', zIndex: 10, display: 'flex', alignItems: 'center', gap: '6px' }}>
						<button
							type="button"
							style={{ ...moverBtn, opacity: index === 0 ? 0.35 : 1 }}
							disabled={index === 0}
							onClick={() => moveBlocksUp([clientId], rootClientId)}
							aria-label={__('Slide naar links', 'snel')}
						>←</button>
						<span style={{ padding: '4px 10px', borderRadius: '9999px', background: 'rgba(0,0,0,.55)', color: 'rgba(255,255,255,.8)', fontSize: '12px' }}>
							{index + 1}/{count}
						</span>
						<button
							type="button"
							style={{ ...moverBtn, opacity: index >= count - 1 ? 0.35 : 1 }}
							disabled={index >= count - 1}
							onClick={() => moveBlocksDown([clientId], rootClientId)}
							aria-label={__('Slide naar rechts', 'snel')}
						>→</button>
					</div>
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
