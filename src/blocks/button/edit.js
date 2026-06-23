/**
 * Snel Button — editor. Previews render.php; fields in sidebar.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const VARIANTS = [
	{ label: 'Outline', value: 'outline' },
	{ label: 'Filled', value: 'filled' },
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const { label, url, variant } = attributes;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Button', 'snel')} initialOpen>
					<SelectControl
						label={__('Style', 'snel')}
						value={variant}
						options={VARIANTS}
						onChange={(v) => setAttributes({ variant: v })}
						__nextHasNoMarginBottom
					/>
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
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender block="snel/button" attributes={attributes} />
			</Disabled>
		</div>
	);
}
