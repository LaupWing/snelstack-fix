/**
 * Button (Gradient) — editor. Previews render.php; fields in sidebar.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const { label, url, showArrow } = attributes;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Button', 'snel')} initialOpen>
					<TextControl
						label={__('Label', 'snel')}
						value={label}
						onChange={(v) => setAttributes({ label: v })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('URL', 'snel')}
						value={url}
						onChange={(v) => setAttributes({ url: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Show arrow', 'snel')}
						checked={showArrow}
						onChange={(v) => setAttributes({ showArrow: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender block="snel/button-gradient" attributes={attributes} />
			</Disabled>
		</div>
	);
}
