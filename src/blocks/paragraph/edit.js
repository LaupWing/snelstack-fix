/**
 * Snel Paragraph — inline RichText. Size in the sidebar; Bold / Italic / Muted
 * from the toolbar. Self-contained styling (classes.js → theme.css), so editor
 * and front end match.
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { textClass, SIZE_OPTIONS, ALIGN_OPTIONS, LEADING_OPTIONS } from './classes';

export default function Edit({ attributes, setAttributes }) {
	const { content, size, align, leading } = attributes;
	const blockProps = useBlockProps({ className: textClass(size, align, leading) });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Paragraph', 'snel')} initialOpen>
					<SelectControl
						label={__('Size', 'snel')}
						value={size}
						options={SIZE_OPTIONS}
						onChange={(v) => setAttributes({ size: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Align', 'snel')}
						value={align}
						options={ALIGN_OPTIONS}
						onChange={(v) => setAttributes({ align: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Leading', 'snel')}
						value={leading}
						options={LEADING_OPTIONS}
						onChange={(v) => setAttributes({ leading: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<RichText
				{...blockProps}
				tagName="p"
				value={content}
				onChange={(v) => setAttributes({ content: v })}
				placeholder={__('Paragraph…', 'snel')}
				allowedFormats={['core/bold', 'core/italic', 'snel/muted']}
			/>
		</>
	);
}
