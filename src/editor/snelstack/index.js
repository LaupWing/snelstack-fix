/**
 * Snelstack Editor Sidebar — Translations.
 *
 * One post per language, linked by a translation group. This panel shows the
 * current page's language, links to existing translations, and creates a new
 * translation (duplicate + AI-translate) in a selectable target language.
 */
import './muted-format';  // registers the snel/muted inline format (toolbar toggle)
import './accent-format'; // registers the snel/accent inline format (toolbar toggle)
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/editor';
import { useState } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { PanelBody, SelectControl, Button, ExternalLink } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// ─── Data ─────────────────────────────────────────────────────────────────

function getData() {
	return window.snelCreateTranslation || null;
}

// ─── Translations Panel ─────────────────────────────────────────────────────

function TranslationsPanel() {
	const data = getData();
	const { savePost } = useDispatch('core/editor');

	const languages = data?.languages || [];
	const current = languages.find((l) => l.isCurrent);
	const others = languages.filter((l) => !l.isCurrent);
	const missing = others.filter((l) => !l.postId);
	const isDefault = data?.currentLang === data?.defaultLang;
	const source = languages.find((l) => l.code === data?.defaultLang);

	// Default the dropdown to the first language without a translation yet.
	const [target, setTarget] = useState((missing[0] || others[0])?.code || '');
	const [busy, setBusy] = useState(false);
	const [status, setStatus] = useState('');
	const [result, setResult] = useState(null);

	if (!data || !data.postId) {
		return (
			<p style={{ color: '#999', fontSize: 13 }}>
				{__('Save this page first to manage its translations.', 'snel')}
			</p>
		);
	}

	const targetLang = others.find((l) => l.code === target);

	const handleCreate = async () => {
		if (!targetLang) return;
		setBusy(true);
		setResult(null);
		setStatus(__('Saving page…', 'snel'));

		const postId = data.postId || window.wp?.data?.select('core/editor')?.getCurrentPostId();

		try {
			await savePost();
		} catch (e) { /* ignore — continue with last saved content */ }

		setStatus(__('Translating… this can take a moment.', 'snel'));

		try {
			const body = new URLSearchParams({
				action: 'snel_create_translation',
				nonce: data.nonce,
				post_id: postId,
				target: target,
			});
			const res = await fetch(data.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
			const raw = await res.text();

			let json;
			try { json = JSON.parse(raw); } catch (e) {
				setStatus(__('Unexpected response. HTTP', 'snel') + ' ' + res.status);
				setBusy(false);
				return;
			}

			if (json.success && json.data && json.data.edit_url) {
				setResult(json.data);
				setStatus(json.data.existed ? __('Already exists:', 'snel') : __('Created!', 'snel'));
			} else {
				setStatus(__('Error:', 'snel') + ' ' + ((json.data && (json.data.message || json.data)) || 'unknown'));
			}
		} catch (err) {
			setStatus(__('Request failed:', 'snel') + ' ' + err.message);
		}
		setBusy(false);
	};

	return (
		<div>
			{/* Current language */}
			<div style={{ marginBottom: 16, padding: '8px 12px', background: '#f5f5f5', borderRadius: 6, fontSize: 13 }}>
				<span style={{ fontSize: 11, fontWeight: 600, color: '#999', textTransform: 'uppercase' }}>
					{__('This page', 'snel')}
				</span>
				<div style={{ marginTop: 4, color: '#333', fontWeight: 600 }}>
					{current ? current.label : '—'}
				</div>
			</div>

			{/* Existing translations */}
			{others.some((l) => l.postId) && (
				<div style={{ marginBottom: 16 }}>
					<span style={{ fontSize: 11, fontWeight: 600, color: '#999', textTransform: 'uppercase', display: 'block', marginBottom: 8 }}>
						{__('Translations', 'snel')}
					</span>
					{others.filter((l) => l.postId).map((l) => (
						<div key={l.code} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '6px 0', borderBottom: '1px solid #eee' }}>
							<span style={{ fontWeight: 600 }}>
								{l.label}
								{l.status && l.status !== 'publish' && (
									<em style={{ color: '#b26200', fontWeight: 400 }}> · {l.status}</em>
								)}
							</span>
							<span style={{ fontSize: 12 }}>
								<a href={l.editUrl}>{__('Edit', 'snel')}</a>
								{l.viewUrl && <> · <ExternalLink href={l.viewUrl}>{__('View', 'snel')}</ExternalLink></>}
							</span>
						</div>
					))}
				</div>
			)}

			{/* Create a new translation — only from the default (source) language */}
			{!isDefault ? (
				<p style={{ fontSize: 13, color: '#666' }}>
					{__('Translations are created from the source language.', 'snel')}
					{source && source.editUrl && (
						<> <a href={source.editUrl}>{__('Open the', 'snel')} {source.label} {__('source →', 'snel')}</a></>
					)}
				</p>
			) : others.length > 0 ? (
				<>
					<SelectControl
						label={__('Target language', 'snel')}
						value={target}
						options={others.map((l) => ({
							label: l.postId ? `${l.label} ✓` : l.label,
							value: l.code,
						}))}
						onChange={setTarget}
						__nextHasNoMarginBottom
					/>

					{targetLang && targetLang.postId ? (
						<p style={{ fontSize: 12, color: '#666', marginTop: 8 }}>
							{__('This language already exists — edit it above.', 'snel')}
						</p>
					) : (
						<Button
							variant="primary"
							onClick={handleCreate}
							isBusy={busy}
							disabled={busy || !targetLang}
							style={{ width: '100%', justifyContent: 'center', marginTop: 8 }}
						>
							{busy
								? __('Working…', 'snel')
								: `✦ ${__('Create', 'snel')} ${targetLang ? targetLang.label : ''}`}
						</Button>
					)}
				</>
			) : (
				<p style={{ color: '#999', fontSize: 13 }}>
					{__('Only one language is configured.', 'snel')}
				</p>
			)}

			{status && <p style={{ marginTop: 8, fontSize: 12, color: '#666' }}>{status}</p>}

			{result && result.edit_url && (
				<div style={{ marginTop: 8, padding: '8px 12px', background: '#edfaef', border: '1px solid #b6e0bf', borderRadius: 6 }}>
					<a href={result.edit_url} style={{ fontWeight: 600 }}>
						{__('Open translation →', 'snel')}
					</a>
					<div style={{ fontSize: 11, color: '#666', marginTop: 2 }}>
						{__('Post ID', 'snel')}: {result.post_id}
					</div>
				</div>
			)}
		</div>
	);
}

// ─── Sidebar ────────────────────────────────────────────────────────────────

function SidebarContent() {
	return (
		<PanelBody title={__('Translations', 'snel')} initialOpen icon="translation">
			<TranslationsPanel />
		</PanelBody>
	);
}

// ─── Icon & Plugin Registration ─────────────────────────────────────────────

const SnelIcon = () => (
	<span className="snel-editor-icon" style={{ display: 'inline-block', width: 20, height: 20, borderRadius: '50%', position: 'relative', overflow: 'hidden', background: 'linear-gradient(135deg, #3b82f6, #7c3aed)' }}>
		<span className="snel-gradient-ring" style={{ display: 'none', position: 'absolute', top: '50%', left: '50%', width: 30, height: 30, background: 'conic-gradient(from 0deg, #06b6d4, #3b82f6, #8b5cf6, #d946ef, #f43f5e, #f97316, #eab308, #22c55e, #06b6d4)', animation: 'snel-editor-gradient-spin 3s linear infinite', zIndex: 1 }} />
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%, -50%)', width: 14, height: 14, zIndex: 2 }}>
			<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" fill="#fff" />
		</svg>
	</span>
);

registerPlugin('snel-editor-sidebar', {
	render: () => (
		<>
			<style>{`
				@keyframes snel-editor-gradient-spin {
					0%   { transform: translate(-50%, -50%) rotate(0deg); }
					100% { transform: translate(-50%, -50%) rotate(360deg); }
				}
				button.components-button[aria-label="Snel Stack"].is-pressed .snel-editor-icon,
				button.components-button[aria-label="Snel Stack"][aria-pressed="true"] .snel-editor-icon,
				button.components-button[aria-label="Snel Stack"][aria-expanded="true"] .snel-editor-icon {
					overflow: hidden;
				}
				button.components-button[aria-label="Snel Stack"].is-pressed .snel-editor-icon .snel-gradient-ring,
				button.components-button[aria-label="Snel Stack"][aria-pressed="true"] .snel-editor-icon .snel-gradient-ring,
				button.components-button[aria-label="Snel Stack"][aria-expanded="true"] .snel-editor-icon .snel-gradient-ring {
					display: block !important;
				}
			`}</style>
			<PluginSidebar
				name="snel-editor-sidebar"
				title={__('Snel Stack', 'snel')}
				icon={<SnelIcon />}
				isPinnable={true}
			>
				<SidebarContent />
			</PluginSidebar>
		</>
	),
});
