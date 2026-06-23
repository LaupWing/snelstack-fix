/**
 * Snel Heading — inline RichText. H-level (tag) in the toolbar; Size in the
 * sidebar (independent of the tag). Brand sizes baked in (classes.js), so editor
 * and front end match without touching core/heading. Select text → Bold /
 * Italic / Muted from the toolbar.
 */
import { useBlockProps, RichText, BlockControls, HeadingLevelDropdown, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { headingClass, SIZE_OPTIONS, WEIGHT_OPTIONS, ALIGN_OPTIONS } from './classes';

export default function Edit({ attributes, setAttributes }) {
	const { content, level, size, weight, align, gradient } = attributes;
	const blockProps = useBlockProps({ className: headingClass(level, size, weight, align, gradient) });
	const levelNumber = parseInt(level.replace('h', ''), 10) || 2;

	return (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={levelNumber}
					options={[1, 2, 3, 4]}
					onChange={(n) => setAttributes({ level: `h${n}` })}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={__('Heading', 'snel')} initialOpen>
					<SelectControl
						label={__('Size', 'snel')}
						value={size}
						options={SIZE_OPTIONS}
						onChange={(v) => setAttributes({ size: v })}
						help={__('Visual size — independent of the H-level (tag).', 'snel')}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Weight', 'snel')}
						value={weight}
						options={WEIGHT_OPTIONS}
						onChange={(v) => setAttributes({ weight: v })}
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={__('Align', 'snel')}
						value={align}
						options={ALIGN_OPTIONS}
						onChange={(v) => setAttributes({ align: v })}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={__('Animated gradient', 'snel')}
						checked={!!gradient}
						onChange={(v) => setAttributes({ gradient: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<RichText
				{...blockProps}
				tagName={level}
				value={content}
				onChange={(v) => setAttributes({ content: v })}
				placeholder={__('Heading…', 'snel')}
				allowedFormats={['core/bold', 'core/italic', 'snel/muted']}
			/>
		</>
	);
}
