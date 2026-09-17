import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit({ attributes, setAttributes }) {
	const { redirect } = attributes;
	const blockProps = useBlockProps({ className: 'bg-white py-16' });

	const inputCls = 'w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400';

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Na verzenden', 'snel')}>
					<ToggleControl
						label={__('Doorsturen naar bedankpagina', 'snel')}
						help={
							redirect
								? __('Bezoeker gaat na verzenden naar de bedankpagina uit Snelstack → Contact. Is daar geen pagina gekozen, dan valt het formulier terug op de inline melding.', 'snel')
								: __('Bezoeker blijft op het formulier en ziet een inline bevestiging.', 'snel')
						}
						checked={redirect}
						onChange={(v) => setAttributes({ redirect: v })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div className="mx-auto max-w-5xl px-4 md:px-8">
					<div className="mx-auto max-w-2xl space-y-6 opacity-60 pointer-events-none select-none">
						<div className="grid grid-cols-2 gap-6">
							<div className="space-y-1.5">
								<label className="block text-sm font-medium text-slate-700">Naam</label>
								<input className={inputCls} placeholder="Jan de Vries" readOnly />
							</div>
							<div className="space-y-1.5">
								<label className="block text-sm font-medium text-slate-700">E-mailadres</label>
								<input className={inputCls} placeholder="jan@bedrijf.nl" readOnly />
							</div>
						</div>
						<div className="space-y-1.5">
							<label className="block text-sm font-medium text-slate-700">Telefoonnummer <span className="text-slate-400">(optioneel)</span></label>
							<input className={inputCls} placeholder="+31 6 12 34 56 78" readOnly />
						</div>
						<div className="space-y-1.5">
							<label className="block text-sm font-medium text-slate-700">Waar gaat het over? <span className="text-slate-400">(optioneel)</span></label>
							<select className={inputCls} disabled>
								<option>Weet ik nog niet</option>
							</select>
							<p className="text-xs text-slate-400">Diensten komen live uit het Services-menu.</p>
						</div>
						<div className="space-y-1.5">
							<label className="block text-sm font-medium text-slate-700">Bericht</label>
							<textarea className={inputCls + ' resize-none'} rows={5} placeholder="Vertel ons over jouw project..." readOnly />
						</div>
						<div className="flex items-center justify-between">
							<span className="inline-flex h-12 items-center justify-center rounded-full bg-slate-100 px-6 text-sm font-semibold text-slate-400 ring-1 ring-slate-200">
								Verstuur bericht
							</span>
							<p className="text-xs text-slate-400">Webhook + bedankpagina instelbaar via Snelstack → Contact</p>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}
