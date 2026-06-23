/**
 * Partners — editor preview via ServerSideRender (renders the live marquee).
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, RangeControl } from '@wordpress/components';
import SectionControl from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { bg, animated, count, size, disableTop, disableBottom } = attributes;
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
				<PanelBody title="Display" initialOpen={true}>
					<ToggleControl
						label="Animated marquee"
						checked={animated}
						onChange={(v) => setAttributes({ animated: v })}
					/>
					<RangeControl
						label="Show (0 = all)"
						value={count}
						onChange={(v) => setAttributes({ count: v })}
						min={0}
						max={20}
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender block="snel/partners" attributes={attributes} />
		</div>
	);
}
