/**
 * Snel Lead Demo — editor view.
 *
 * Heading and body are inline RichText. The channel-button labels live in the
 * sidebar. The form itself is a non-interactive preview; the real behaviour is
 * in view.js on the front end.
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl, { getSectionStyle, getSectionClass, getSectionPaddingClass } from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { heading, body, emailLabel, whatsappLabel, bg, size, disableTop, disableBottom } = attributes;

	const blockProps = useBlockProps({
		className: `snel-lead-demo ${getSectionClass(bg)}`.trim(),
		style: getSectionStyle(bg),
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Channel buttons', 'snel')} initialOpen>
					<TextControl
						label={__('E-mail button', 'snel')}
						value={emailLabel}
						onChange={(v) => setAttributes({ emailLabel: v })}
						__nextHasNoMarginBottom
					/>
					<TextControl
						label={__('WhatsApp button', 'snel')}
						value={whatsappLabel}
						onChange={(v) => setAttributes({ whatsappLabel: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>
			</InspectorControls>

			<section {...blockProps}>
				<div className={`mx-auto w-full max-w-3xl px-4 md:px-8 ${getSectionPaddingClass(size, disableTop, disableBottom)}`}>
					<RichText
						tagName="h2"
						className="snel-heading snel-h-2xl"
						value={heading}
						onChange={(v) => setAttributes({ heading: v })}
						placeholder={__('Heading…', 'snel')}
						allowedFormats={['core/bold', 'core/italic', 'snel/muted', 'snel/accent']}
					/>

					<RichText
						tagName="p"
						className="snel-text snel-text-lg mt-5 max-w-2xl"
						value={body}
						onChange={(v) => setAttributes({ body: v })}
						placeholder={__('Intro…', 'snel')}
						allowedFormats={['core/bold', 'core/italic', 'snel/muted', 'snel/accent']}
					/>

					<div className="mt-8 flex flex-wrap items-center gap-3 select-none">
						<span className="snel-ld-choice is-active">{emailLabel}</span>
						<span className="snel-ld-choice">{whatsappLabel}</span>
					</div>

					<div className="mt-8 grid grid-cols-1 gap-5 opacity-60 sm:grid-cols-2 pointer-events-none select-none">
						<div>
							<span className="snel-ld-label">{__('Naam', 'snel')}</span>
							<input className="snel-ld-input" placeholder="Jan de Vries" readOnly />
						</div>
						<div>
							<span className="snel-ld-label">{__('E-mailadres', 'snel')}</span>
							<input className="snel-ld-input" placeholder="jan@bedrijf.nl" readOnly />
						</div>
					</div>

					<p className="mt-6 text-xs opacity-50">
						{__('Preview — de kanaalkeuze, het WhatsApp-veld en het versturen werken op de front-end. Webhook: Snelstack → Contact.', 'snel')}
					</p>
				</div>
			</section>
		</>
	);
}
