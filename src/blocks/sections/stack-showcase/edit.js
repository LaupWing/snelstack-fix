/**
 * Stack Showcase — editor. Slides are an editable attribute array (one plate +
 * card per slide). The R3F preview renders the current slides directly.
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl, TextareaControl, Button, Flex, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import StackShowcase from './StackShowcase';
import { DEFAULT_SLIDES } from './slides';
import SectionControl, { getSectionStyle, getSectionClass, getSectionPaddingClass } from '../../components/SectionControl';

const EMPTY_SLIDE = { title: '', dot: '#38bdf8', text: '', cta: '', url: '#' };

export default function Edit({ attributes, setAttributes }) {
	const { bg, size, disableTop, disableBottom } = attributes;
	const slides = attributes.slides && attributes.slides.length ? attributes.slides : DEFAULT_SLIDES;

	const blockProps = useBlockProps({
		className: `${getSectionClass(bg)} ${getSectionPaddingClass(size, disableTop, disableBottom)}`,
		style: getSectionStyle(bg),
	});

	const update = (i, key, val) =>
		setAttributes({ slides: slides.map((s, idx) => idx === i ? { ...s, [key]: val } : s) });

	const move = (i, dir) => {
		const j = i + dir;
		if (j < 0 || j >= slides.length) return;
		const next = [...slides];
		[next[i], next[j]] = [next[j], next[i]];
		setAttributes({ slides: next });
	};

	const remove = (i) =>
		setAttributes({ slides: slides.filter((_, idx) => idx !== i) });

	const add = () =>
		setAttributes({ slides: [...slides, { ...EMPTY_SLIDE }] });

	return (
		<div {...blockProps}>
			<InspectorControls>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>

				{slides.map((s, i) => (
					<PanelBody
						key={i}
						title={`${i + 1} — ${s.title || __('(geen titel)', 'snel')}`}
						initialOpen={i === 0}
					>
						<TextControl
							label={__('Titel', 'snel')}
							value={s.title}
							onChange={(v) => update(i, 'title', v)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Kleur (plaat + stip)', 'snel')}
							type="color"
							value={s.dot || '#38bdf8'}
							onChange={(v) => update(i, 'dot', v)}
							__nextHasNoMarginBottom
						/>
						<TextareaControl
							label={__('Tekst', 'snel')}
							value={s.text}
							onChange={(v) => update(i, 'text', v)}
							rows={3}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Knop tekst', 'snel')}
							value={s.cta}
							onChange={(v) => update(i, 'cta', v)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Knop URL', 'snel')}
							type="url"
							value={s.url}
							onChange={(v) => update(i, 'url', v)}
							__nextHasNoMarginBottom
						/>
						<PanelRow>
							<Flex gap={2} wrap>
								<FlexItem>
									<Button size="small" variant="secondary" onClick={() => move(i, -1)} disabled={i === 0}>↑</Button>
								</FlexItem>
								<FlexItem>
									<Button size="small" variant="secondary" onClick={() => move(i, 1)} disabled={i === slides.length - 1}>↓</Button>
								</FlexItem>
								<FlexItem>
									<Button size="small" variant="secondary" isDestructive onClick={() => remove(i)} disabled={slides.length <= 1}>
										{__('Verwijder', 'snel')}
									</Button>
								</FlexItem>
							</Flex>
						</PanelRow>
					</PanelBody>
				))}

				<PanelBody>
					<Button variant="secondary" onClick={add} style={{ width: '100%' }}>
						+ {__('Plaat toevoegen', 'snel')}
					</Button>
				</PanelBody>
			</InspectorControls>

			<div className="px-4 md:px-8">
				<div className="mx-auto w-full max-w-5xl">
					<StackShowcase slides={slides} />
				</div>
			</div>
		</div>
	);
}
