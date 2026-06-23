/**
 * Snel Statement — editor view. Two inline RichText fields (heading left,
 * paragraph right). Word-reveal animation runs on the front end only (view.js).
 */
import { useBlockProps, RichText, InspectorControls, BlockControls, HeadingLevelDropdown } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { headingClass } from '../../heading/classes';
import SectionControl, { getSectionStyle, getSectionClass, getSectionPaddingClass } from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { heading, paragraph, level, bg, size, disableTop, disableBottom } = attributes;
	const levelNumber = parseInt(level.replace('h', ''), 10) || 3;

	const blockProps = useBlockProps({
		className: `snel-statement ${getSectionClass(bg)}`.trim(),
		style: getSectionStyle(bg),
	});

	return (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={levelNumber}
					options={[2, 3, 4]}
					onChange={(n) => setAttributes({ level: `h${n}` })}
				/>
			</BlockControls>

			<InspectorControls>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>
			</InspectorControls>

			<section {...blockProps}>
				<div className={`mx-auto w-full max-w-5xl px-4 md:px-8 ${getSectionPaddingClass(size, disableTop, disableBottom)}`}>
					<div className="flex flex-col gap-16 lg:flex-row lg:gap-32">
						<div className="flex-none lg:w-72">
							<RichText
								tagName={level}
								className={headingClass(level, '2xl')}
								value={heading}
								onChange={(v) => setAttributes({ heading: v })}
								placeholder={__('Heading…', 'snel')}
								allowedFormats={['core/bold', 'core/italic', 'snel/muted', 'snel/accent']}
							/>
						</div>
						<div className="grow">
							<RichText
								tagName="div"
								className="snel-text snel-text-xl [&_p+p]:mt-8"
								value={paragraph}
								onChange={(v) => setAttributes({ paragraph: v })}
								placeholder={__('Paragraph… (Enter for a new paragraph)', 'snel')}
								allowedFormats={['core/bold', 'core/italic', 'snel/muted', 'snel/accent']}
							/>
						</div>
					</div>
				</div>
			</section>
		</>
	);
}
