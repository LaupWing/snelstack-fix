import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { bg, perPage } = attributes;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<SectionControl value={bg} onChange={(v) => setAttributes({ bg: v })} />
				<PanelBody title={__('Instellingen', 'snel')} initialOpen>
					<RangeControl
						label={__('Posts per pagina', 'snel')}
						value={perPage}
						onChange={(v) => setAttributes({ perPage: v })}
						min={3}
						max={24}
						step={3}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender block="snel/posts-archive" attributes={attributes} />
		</div>
	);
}
