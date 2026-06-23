import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit({ attributes, setAttributes }) {
	const { label1, value1, label2, value2, label3, value3, backUrl, backLabel } = attributes;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Navigatie', 'snel')} initialOpen>
					<TextControl label={__('Back URL', 'snel')} value={backUrl} onChange={(v) => setAttributes({ backUrl: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Back Label', 'snel')} value={backLabel} onChange={(v) => setAttributes({ backLabel: v })} __nextHasNoMarginBottom />
				</PanelBody>
				<PanelBody title={__('Info kaarten', 'snel')} initialOpen>
					<TextControl label={__('Label 1', 'snel')} value={label1} onChange={(v) => setAttributes({ label1: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Waarde 1', 'snel')} value={value1} onChange={(v) => setAttributes({ value1: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Label 2', 'snel')} value={label2} onChange={(v) => setAttributes({ label2: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Waarde 2', 'snel')} value={value2} onChange={(v) => setAttributes({ value2: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Label 3', 'snel')} value={label3} onChange={(v) => setAttributes({ label3: v })} __nextHasNoMarginBottom />
					<TextControl label={__('Waarde 3', 'snel')} value={value3} onChange={(v) => setAttributes({ value3: v })} __nextHasNoMarginBottom />
				</PanelBody>
			</InspectorControls>
			<ServerSideRender block="snel/thumbnail" attributes={attributes} />
		</div>
	);
}

registerBlockType(metadata.name, { edit: Edit });
