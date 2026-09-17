document.querySelectorAll('.snel-contact-form').forEach((form) => {
	const btn      = form.querySelector('.snel-cf-submit');
	const btnLabel = form.querySelector('.snel-cf-btn-label');
	const status   = form.querySelector('.snel-cf-status');
	const sendLabel = btnLabel ? btnLabel.textContent.trim() : '';

	const setStatus = (msg, type) => {
		if (!status) return;
		status.textContent = msg;
		status.className = 'snel-cf-status text-sm font-medium ' + (type === 'success' ? 'text-green-600' : 'text-red-600');
	};

	// ?dienst=<slug> preselects a service. render.php already does this
	// server-side; repeating it here keeps it correct behind a page cache.
	const wanted = new URLSearchParams(window.location.search).get('dienst');
	const serviceSelect = form.querySelector('select[name="service"]');
	if (wanted && serviceSelect && serviceSelect.querySelector(`option[value="${CSS.escape(wanted)}"]`)) {
		serviceSelect.value = wanted;
	}

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		if (btn?.disabled) return;

		// Loading
		if (btn) btn.disabled = true;
		if (btnLabel) btnLabel.textContent = btnLabel.dataset.sending || 'Versturen…';
		if (status) status.className = 'snel-cf-status hidden';

		const payload = {
			name:    form.querySelector('[name="name"]')?.value.trim()    ?? '',
			email:   form.querySelector('[name="email"]')?.value.trim()   ?? '',
			phone:   form.querySelector('[name="phone"]')?.value.trim()   ?? '',
			message: form.querySelector('[name="message"]')?.value.trim() ?? '',
			service: serviceSelect?.value ?? '',
			lang:    form.dataset.lang || '',
			page:    window.location.href,
		};

		try {
			const res  = await fetch(form.dataset.action, {
				method:  'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce':   form.dataset.nonce,
				},
				body: JSON.stringify(payload),
			});
			const json = await res.json().catch(() => ({}));

			if (res.ok) {
				// Thank-you page configured → go there. Otherwise stay put and
				// show the inline confirmation (both paths stay supported).
				const redirect = form.dataset.redirect;
				if (redirect) {
					window.location.assign(redirect);
					return;
				}
				setStatus(form.dataset.success || json.message || 'Bedankt!', 'success');
				form.reset();
			} else {
				throw new Error(json.message || form.dataset.error || '');
			}
		} catch (err) {
			setStatus(err.message || form.dataset.error || 'Er is iets misgegaan.', 'error');
		} finally {
			if (btn) btn.disabled = false;
			if (btnLabel) btnLabel.textContent = sendLabel || 'Verstuur bericht';
		}
	});
});
