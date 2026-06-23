/**
 * Snel Features — Editor.
 * Cards live as an array attribute. Sidebar handles add/remove/reorder/edit.
 * Canvas shows a live grid preview via FeatureCard component.
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SectionControl, { getSectionStyle, getSectionPaddingClass } from '../../components/SectionControl';
import { ICONS } from './icons';
import FeatureCard from './FeatureCard';

function IconPicker({ value, onChange }) {
	return (
		<div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 6, marginBottom: 12 }}>
			{Object.entries(ICONS).map(([key, def]) => {
				const active = value === key;
				return (
					<button key={key} title={def.label} onClick={() => onChange(key)} style={{
						display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 3,
						padding: 6, borderRadius: 6, cursor: 'pointer',
						border: active ? '2px solid #5eead4' : '2px solid transparent',
						background: active ? 'rgba(94,234,212,0.1)' : 'rgba(0,0,0,0.04)',
						color: active ? '#5eead4' : '#64748b',
					}}>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
							strokeWidth="1" stroke="currentColor" style={{ width: 20, height: 20 }}>
							{def.paths.map((d, i) => <path key={i} strokeLinecap="round" strokeLinejoin="round" d={d} />)}
						</svg>
						<span style={{ fontSize: 8, lineHeight: 1.2, textAlign: 'center' }}>{def.label}</span>
					</button>
				);
			})}
		</div>
	);
}

function updateCard(cards, index, patch) {
	return cards.map((c, i) => i === index ? { ...c, ...patch } : c);
}

export default function Edit({ attributes, setAttributes }) {
	const { cards, bg, size, disableTop, disableBottom } = attributes;
	const blockProps = useBlockProps({
		className: `w-full px-4 md:px-8 ${getSectionPaddingClass(size, disableTop, disableBottom)}`,
		style: getSectionStyle(bg),
	});

	const setCards = (next) => setAttributes({ cards: next });

	return (
		<>
			<InspectorControls>
				<SectionControl
					value={bg} onChange={(v) => setAttributes({ bg: v })}
					size={size} onSizeChange={(v) => setAttributes({ size: v })}
					disableTop={disableTop} onDisableTopChange={(v) => setAttributes({ disableTop: v })}
					disableBottom={disableBottom} onDisableBottomChange={(v) => setAttributes({ disableBottom: v })}
				/>
				{cards.map((card, i) => (
					<PanelBody key={i} title={card.heading || `Card ${i + 1}`} initialOpen={i === 0}>
						<IconPicker value={card.icon} onChange={(v) => setCards(updateCard(cards, i, { icon: v }))} />
						<TextControl
							label={__('Heading', 'snel')}
							value={card.heading}
							onChange={(v) => setCards(updateCard(cards, i, { heading: v }))}
							__nextHasNoMarginBottom
						/>
						<TextareaControl
							label={__('Body', 'snel')}
							value={card.body}
							onChange={(v) => setCards(updateCard(cards, i, { body: v }))}
							rows={3}
							__nextHasNoMarginBottom
						/>
						<div style={{ display: 'flex', gap: 6, marginTop: 8 }}>
							<Button variant="secondary" size="small" disabled={i === 0}
								onClick={() => {
									const next = [...cards];
									[next[i - 1], next[i]] = [next[i], next[i - 1]];
									setCards(next);
								}}>↑</Button>
							<Button variant="secondary" size="small" disabled={i === cards.length - 1}
								onClick={() => {
									const next = [...cards];
									[next[i], next[i + 1]] = [next[i + 1], next[i]];
									setCards(next);
								}}>↓</Button>
							<Button variant="secondary" size="small" isDestructive
								onClick={() => setCards(cards.filter((_, j) => j !== i))}>
								{__('Remove', 'snel')}
							</Button>
						</div>
					</PanelBody>
				))}
				<div style={{ padding: '8px 16px 16px' }}>
					<Button variant="primary" onClick={() => setCards([...cards, { icon: 'star', heading: '', body: '' }])}>
						{__('+ Add card', 'snel')}
					</Button>
				</div>
			</InspectorControls>

			<div {...blockProps}>
				<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
					{cards.map((card, i) => (
						<FeatureCard key={i} {...card} />
					))}
				</div>
			</div>
		</>
	);
}
