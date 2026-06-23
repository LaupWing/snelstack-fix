import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { bg, heading, intro, count, size, disableTop, disableBottom } = attributes;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>
				<PanelBody title={__('Instellingen', 'snel')} initialOpen>
					<TextControl
						label={__('Titel', 'snel')}
						value={heading}
						onChange={(v) => setAttributes({ heading: v })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('Intro', 'snel')}
						value={intro}
						onChange={(v) => setAttributes({ intro: v })}
						__nextHasNoMarginBottom
					/>
					<RangeControl
						label={__('Aantal posts', 'snel')}
						value={count}
						onChange={(v) => setAttributes({ count: v })}
						min={2}
						max={12}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender block="snel/posts" attributes={attributes} />
		</div>
	);
}
