/**
 * Snel Thank You — Editor.
 *
 * Heading and paragraph are editable in place; leave them empty to use the
 * translated defaults from render.php. Calendly button toggled in the sidebar.
 */
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import BackgroundWrapper from '../../components/BackgroundWrapper';
import PanelFrame from '../../components/PanelFrame';
import SectionControl, { getSectionStyle, getSectionClass } from '../../components/SectionControl';

export default function Edit({ attributes, setAttributes }) {
	const { heading, paragraph, showCalendly, calendlyUrl, theme, showBeams, showGradient } = attributes;

	const isDark = theme === 'dark' || theme === 'canvas';
	const fade   = theme === 'canvas' ? 'from-[#020617]' : theme === 'dark' ? 'from-[#2e1065]' : 'from-white';

	const blockProps = useBlockProps({
		className: `snel-thanks ${getSectionClass(theme)}`,
		style: getSectionStyle(theme),
	});

	return (
		<>
			<InspectorControls>
				<SectionControl
					value={theme} onChange={(v) => setAttributes({ theme: v })}
					showBeams={showBeams} onShowBeamsChange={(v) => setAttributes({ showBeams: v })}
					showGradient={showGradient} onShowGradientChange={(v) => setAttributes({ showGradient: v })}
				/>
				<PanelBody title={__('Gesprek inplannen', 'snel')}>
					<ToggleControl
						label={__('Calendly-knop tonen', 'snel')}
						checked={showCalendly}
						onChange={(v) => setAttributes({ showCalendly: v })}
						__nextHasNoMarginBottom
					/>
					{showCalendly && (
						<TextControl
							label={__('Calendly URL', 'snel')}
							value={calendlyUrl}
							onChange={(v) => setAttributes({ calendlyUrl: v })}
							__nextHasNoMarginBottom
						/>
					)}
				</PanelBody>
			</InspectorControls>

			<BackgroundWrapper
				blockProps={blockProps}
				attributes={{ bgPosition: 'absolute', backdrop: 'transparent' }}
				fade={fade}
				showBeams={showBeams}
				showGradient={showGradient}
			>
				<div className="px-4 pt-16 pb-20 md:px-8 lg:pt-20">
					<PanelFrame dark={isDark}>
						<div className="relative z-10 flex flex-col items-center text-center">
							<span className="mb-8 inline-flex size-14 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/30">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" className="size-7"><path d="M20 6 9 17l-5-5" /></svg>
							</span>

							<RichText
								tagName="h1"
								className="snel-heading snel-h-xl max-w-3xl"
								value={heading}
								onChange={(v) => setAttributes({ heading: v })}
								placeholder={__('Bedankt voor je bericht.', 'snel')}
							/>

							<RichText
								tagName="p"
								className="snel-text snel-text-lg mt-6 max-w-2xl"
								value={paragraph}
								onChange={(v) => setAttributes({ paragraph: v })}
								placeholder={__('Je bericht is binnen. Je hebt binnen één werkdag antwoord.', 'snel')}
							/>

							{showCalendly && (
								<div className="mt-10 flex flex-wrap items-center justify-center gap-4 opacity-60">
									<span className="inline-flex h-12 items-center justify-center rounded-full bg-slate-100 px-6 text-sm font-semibold text-slate-500 ring-1 ring-slate-200">
										{__('Plan een gesprek', 'snel')}
									</span>
									<span className="text-sm font-semibold">{__('Terug naar de homepage', 'snel')}</span>
								</div>
							)}
						</div>
					</PanelFrame>
				</div>
			</BackgroundWrapper>
		</>
	);
}
