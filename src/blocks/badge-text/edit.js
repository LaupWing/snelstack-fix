/**
 * Snel Badge (text pill) — editor. Previews render.php; label in the sidebar.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, ToggleControl, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const COLORS = [
	{ label: 'Teal', value: 'teal' },
	{ label: 'Sky', value: 'sky' },
	{ label: 'Violet', value: 'violet' },
	{ label: 'Pink', value: 'pink' },
	{ label: 'Red', value: 'red' },
];

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const { label, color, useCategory } = attributes;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Badge', 'snel')} initialOpen>
					<ToggleControl
						label={__('Use category', 'snel')}
						checked={useCategory}
						onChange={(v) => setAttributes({ useCategory: v })}
						help={__('Shows the post/archive category (translated) instead of the label.', 'snel')}
						__nextHasNoMarginBottom
					/>
					{!useCategory && (
						<TextControl
							label={__('Label', 'snel')}
							value={label}
							onChange={(v) => setAttributes({ label: v })}
							__nextHasNoMarginBottom
						/>
					)}
					<SelectControl
						label={__('Color', 'snel')}
						value={color}
						options={COLORS}
						onChange={(v) => setAttributes({ color: v })}
						help={__('Brand gradient colour — lighter automatically on dark.', 'snel')}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender block="snel/badge-text" attributes={attributes} />
			</Disabled>
		</div>
	);
}
