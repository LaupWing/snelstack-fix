import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { allLabel } = attributes;

	const blockProps = useBlockProps({
		className: 'snel-category-nav flex gap-1 flex-wrap',
	});

	const mockCats = ['WordPress', 'AI & Automatisering', 'SEO', 'MKB Tips'];

	const activeClass  = 'rounded-md bg-slate-950 text-teal-400 text-sm font-medium px-4 h-11 flex items-center whitespace-nowrap';
	const inactiveClass = 'rounded-md text-slate-700 text-sm font-medium px-4 h-11 flex items-center whitespace-nowrap bg-slate-100';

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Category Nav', 'snel')} initialOpen>
					<TextControl
						label={__('Label "Alle"', 'snel')}
						value={allLabel}
						onChange={(v) => setAttributes({ allLabel: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<nav {...blockProps}>
				<span className={activeClass}>{allLabel}</span>
				{mockCats.map((cat) => (
					<span key={cat} className={inactiveClass}>{cat}</span>
				))}
			</nav>
		</>
	);
}
