document.querySelectorAll('.snel-contact-form').forEach((form) => {
	const btn      = form.querySelector('.snel-cf-submit');
	const btnLabel = form.querySelector('.snel-cf-btn-label');
	const status   = form.querySelector('.snel-cf-status');

	const setStatus = (msg, type) => {
		if (!status) return;
		status.textContent = msg;
		status.className = 'snel-cf-status text-sm font-medium ' + (type === 'success' ? 'text-green-600' : 'text-red-600');
	};

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		if (btn?.disabled) return;

		// Loading
		if (btn) btn.disabled = true;
		if (btnLabel) btnLabel.textContent = 'Versturen…';
		if (status) status.className = 'snel-cf-status hidden';

		const payload = {
			name:    form.querySelector('[name="name"]')?.value.trim()    ?? '',
			email:   form.querySelector('[name="email"]')?.value.trim()   ?? '',
			phone:   form.querySelector('[name="phone"]')?.value.trim()   ?? '',
			message: form.querySelector('[name="message"]')?.value.trim() ?? '',
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
				setStatus(json.message || 'Bedankt! We nemen zo snel mogelijk contact op.', 'success');
				form.reset();
			} else {
				throw new Error(json.message || 'Er is iets misgegaan.');
			}
		} catch (err) {
			setStatus(err.message || 'Er is iets misgegaan. Probeer het opnieuw.', 'error');
		} finally {
			if (btn) btn.disabled = false;
			if (btnLabel) btnLabel.textContent = 'Verstuur bericht';
		}
	});
});
