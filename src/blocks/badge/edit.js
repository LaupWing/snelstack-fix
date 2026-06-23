/**
 * Snel Review Badge — editor. Previews render.php via ServerSideRender.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const blockProps = useBlockProps();
	const { score, reviewCount, countLabel } = attributes;

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Review Badge', 'snel')} initialOpen>
					<TextControl
						label={__('Score', 'snel')}
						value={score}
						onChange={(v) => setAttributes({ score: v })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Review count', 'snel')}
						value={reviewCount}
						onChange={(v) => setAttributes({ reviewCount: v })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Count label', 'snel')}
						value={countLabel}
						onChange={(v) => setAttributes({ countLabel: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender block="snel/badge" attributes={attributes} />
			</Disabled>
		</div>
	);
}
