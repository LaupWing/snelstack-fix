document.querySelectorAll('.snel-category-nav').forEach((root) => {
	const track = root.querySelector('.snel-catnav-track');
	const prev  = root.querySelector('.snel-catnav-prev');
	const next  = root.querySelector('.snel-catnav-next');

	if (!track || !prev || !next) return;

	function update() {
		const max = Math.max(0, track.scrollWidth - track.clientWidth);
		const eps = 2;
		prev.classList.toggle('is-visible', track.scrollLeft > eps);
		next.classList.toggle('is-visible', track.scrollLeft < max - eps);
	}

	function step(dir) {
		const max    = Math.max(0, track.scrollWidth - track.clientWidth);
		let   target = track.scrollLeft + dir * track.clientWidth * 0.8;
		// Snap to the edges so one click always reaches the start/end,
		// even when an appearing chevron shifts the track width.
		if (target < 48) target = 0;
		if (target > max - 48) target = max;
		track.scrollTo({ left: target, behavior: 'smooth' });
	}

	prev.addEventListener('click', () => step(-1));
	next.addEventListener('click', () => step(1));
	track.addEventListener('scroll', update, { passive: true });
	window.addEventListener('resize', update, { passive: true });

	// Center the active category if it starts off-screen.
	const active = track.querySelector('[aria-current="page"]');
	if (active) {
		const target = active.offsetLeft - (track.clientWidth - active.offsetWidth) / 2;
		if (target > 0) track.scrollLeft = target;
	}

	update();
});
