/**
 * Snel Lead Demo — front end.
 *
 * Pick a channel → the form panel reveals (opacity + translateY only, both
 * composited) → submit posts to the existing contact endpoint.
 *
 * All user-facing strings come from data-attributes written by render.php with
 * snel__(), so the Snel Translations plugin controls them — nothing is
 * hardcoded here.
 */
document.querySelectorAll('.snel-lead-demo').forEach((root) => {
	const form  = root.querySelector('.snel-ld-form');
	const panel = root.querySelector('.snel-ld-panel');
	if (!form || !panel) return;

	const choices    = root.querySelectorAll('.snel-ld-choice');
	const phoneField = form.querySelector('.snel-ld-field-phone');
	const phoneInput = form.querySelector('[name="phone"]');
	const nameInput  = form.querySelector('[name="name"]');
	const emailInput = form.querySelector('[name="email"]');
	const btn        = form.querySelector('.snel-ld-submit');
	const btnLabel   = form.querySelector('.snel-ld-submit-label');
	const status     = form.querySelector('.snel-ld-status');
	const d          = form.dataset;

	let channel = '';
	let done    = false;

	const setStatus = (msg, type) => {
		if (!status) return;
		status.textContent = msg;
		status.classList.toggle('is-error', type === 'error');
		status.classList.toggle('is-success', type === 'success');
		status.hidden = !msg;
	};

	const select = (value) => {
		if (done) return;
		channel = value;

		choices.forEach((c) => {
			const on = c.dataset.channel === value;
			c.classList.toggle('is-active', on);
			c.setAttribute('aria-pressed', on ? 'true' : 'false');
		});

		const wantsWhatsapp = value === 'whatsapp';
		if (phoneField) phoneField.hidden = !wantsWhatsapp;
		if (phoneInput) phoneInput.required = wantsWhatsapp;

		if (panel.hidden) {
			panel.hidden = false;
			// Next frame so the transition has a start value to animate from.
			requestAnimationFrame(() => panel.classList.add('is-open'));
		}

		const first = [nameInput, emailInput, phoneInput].find((i) => i && !i.disabled && !i.value);
		if (first) first.focus({ preventScroll: true });
	};

	choices.forEach((c) => c.addEventListener('click', () => select(c.dataset.channel)));

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		if (!btn || btn.disabled || done) return;

		// Native validation, but only after a channel is chosen (phone is only
		// required in the WhatsApp flow).
		if (!form.checkValidity()) {
			form.reportValidity();
			return;
		}

		btn.disabled = true;
		if (btnLabel) btnLabel.textContent = d.labelSending;
		setStatus('', null);

		const payload = {
			name:    nameInput?.value.trim()  ?? '',
			email:   emailInput?.value.trim() ?? '',
			phone:   channel === 'whatsapp' ? (phoneInput?.value.trim() ?? '') : '',
			// `channel` is the field the backend routes on; see render.php.
			channel,
			source_block: 'lead-demo',
			lang: d.lang || '',
			page: window.location.href,
			message: channel === 'whatsapp' ? d.messageWhatsapp : d.messageEmail,
		};

		try {
			const res  = await fetch(d.action, {
				method:  'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': d.nonce },
				body:    JSON.stringify(payload),
			});
			const json = await res.json().catch(() => ({}));

			if (!res.ok) throw new Error(json.message || d.msgError);

			// json.redirect (thank-you page) is intentionally ignored: this block
			// lives on a one-pager that must not hand the visitor an exit.
			done = true;
			setStatus(d.msgSuccess, 'success');
			form.reset();
			if (phoneField) phoneField.hidden = true;
		} catch (err) {
			setStatus(err.message || d.msgError, 'error');
		} finally {
			btn.disabled = done;
			if (btnLabel) btnLabel.textContent = d.labelSend;
		}
	});
});
