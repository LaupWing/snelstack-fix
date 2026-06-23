import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { bg, showAll, size, disableTop, disableBottom } = attributes;
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
				<PanelBody title={__('Cases', 'snel')} initialOpen={false}>
					<ToggleControl
						label={__('Toon alle cases', 'snel')}
						help={showAll ? __('Alle cases worden getoond', 'snel') : __('Toont de 4 laatste cases', 'snel')}
						checked={showAll}
						onChange={(v) => setAttributes({ showAll: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender block="snel/cases" attributes={attributes} />
		</div>
	);
}
