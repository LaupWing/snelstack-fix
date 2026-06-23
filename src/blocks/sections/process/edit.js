/**
 * Snel Process — editor.
 *
 * Steps are stored as an attribute array. Each step has: n, title, heading,
 * body, btn_label, btn_url. The sidebar shows one collapsible panel per step
 * with TextControl / TextareaControl fields plus move-up/down and delete.
 * ServerSideRender keeps the live SVG preview in sync without duplicating the
 * complex geometry logic in JS.
 */
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl, TextareaControl, Button, Flex, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl from '../../components/SectionControl';

const EMPTY_STEP = { n: '', title: '', heading: '', body: '', btn_label: 'Meer info', btn_url: '#' };

export default function Edit({ attributes, setAttributes }) {
	const { theme, size, disableTop, disableBottom, steps } = attributes;
	const blockProps = useBlockProps();

	const update = (i, key, val) =>
		setAttributes({ steps: steps.map((s, idx) => idx === i ? { ...s, [key]: val } : s) });

	const move = (i, dir) => {
		const j = i + dir;
		if (j < 0 || j >= steps.length) return;
		const next = [...steps];
		[next[i], next[j]] = [next[j], next[i]];
		setAttributes({ steps: next });
	};

	const remove = (i) =>
		setAttributes({ steps: steps.filter((_, idx) => idx !== i) });

	const add = () => {
		const n = String(steps.length + 1).padStart(2, '0');
		setAttributes({ steps: [...steps, { ...EMPTY_STEP, n }] });
	};

	return (
		<div {...blockProps}>
			<InspectorControls>
				<SectionControl
					value={theme} onChange={(v) => setAttributes({ theme: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>

				{steps.map((s, i) => (
					<PanelBody
						key={i}
						title={`${s.n || `0${i + 1}`} — ${s.title || __('(geen titel)', 'snel')}`}
						initialOpen={i === 0}
					>
						<TextControl
							label={__('Nummer', 'snel')}
							value={s.n}
							onChange={(v) => update(i, 'n', v)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Label links', 'snel')}
							value={s.title}
							onChange={(v) => update(i, 'title', v)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Kaart kop', 'snel')}
							value={s.heading}
							onChange={(v) => update(i, 'heading', v)}
							__nextHasNoMarginBottom
						/>
						<TextareaControl
							label={__('Kaart tekst', 'snel')}
							value={s.body}
							onChange={(v) => update(i, 'body', v)}
							rows={3}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Knop tekst', 'snel')}
							value={s.btn_label}
							onChange={(v) => update(i, 'btn_label', v)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Knop URL', 'snel')}
							type="url"
							value={s.btn_url}
							onChange={(v) => update(i, 'btn_url', v)}
							__nextHasNoMarginBottom
						/>
						<PanelRow>
							<Flex gap={2} wrap>
								<FlexItem>
									<Button size="small" variant="secondary" onClick={() => move(i, -1)} disabled={i === 0}>↑</Button>
								</FlexItem>
								<FlexItem>
									<Button size="small" variant="secondary" onClick={() => move(i, 1)} disabled={i === steps.length - 1}>↓</Button>
								</FlexItem>
								<FlexItem>
									<Button size="small" variant="secondary" isDestructive onClick={() => remove(i)} disabled={steps.length <= 1}>
										{__('Verwijder', 'snel')}
									</Button>
								</FlexItem>
							</Flex>
						</PanelRow>
					</PanelBody>
				))}

				<PanelBody>
					<Button variant="secondary" onClick={add} style={{ width: '100%' }}>
						+ {__('Stap toevoegen', 'snel')}
					</Button>
				</PanelBody>
			</InspectorControls>

			<ServerSideRender block="snel/process" attributes={attributes} />
		</div>
	);
}
