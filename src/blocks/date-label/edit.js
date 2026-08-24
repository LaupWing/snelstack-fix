/**
 * Snel Date Label — editor. Previews render.php; format in the sidebar.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const { format, showModified } = attributes;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Date', 'snel')} initialOpen>
					<ToggleControl
						label={__('Use modified date', 'snel')}
						checked={showModified}
						onChange={(v) => setAttributes({ showModified: v })}
						help={__('Shows the last-updated date instead of the publish date.', 'snel')}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Format', 'snel')}
						value={format}
						onChange={(v) => setAttributes({ format: v })}
						help={__('PHP date format, e.g. "j F Y". Empty = site setting.', 'snel')}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender block="snel/date-label" attributes={attributes} />
			</Disabled>
		</div>
	);
}
