/**
 * Stack Showcase — editor preview. Renders the R3F component directly.
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import StackShowcase from './StackShowcase';
import { DEFAULT_SLIDES } from './slides';
import SectionControl, { getSectionStyle, getSectionClass, getSectionPaddingClass } from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { bg, size, disableTop, disableBottom } = attributes;
	const blockProps = useBlockProps({
		className: `${getSectionClass(bg)} ${getSectionPaddingClass(size, disableTop, disableBottom)}`,
		style: getSectionStyle(bg),
	});

	return (
		<div {...blockProps}>
			<InspectorControls>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>
			</InspectorControls>
			<div className="px-4 md:px-8">
				<div className="mx-auto w-full max-w-5xl">
					<StackShowcase slides={DEFAULT_SLIDES} />
				</div>
			</div>
		</div>
	);
}
