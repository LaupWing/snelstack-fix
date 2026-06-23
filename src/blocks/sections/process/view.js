

/**
 * Snel Process — drive the pulse position from the scroll.
 *
 * Sets --snel-p (0–1) on each .snel-flow-track = the fraction of the section
 * currently at the centre of the viewport. CSS positions the lit pulse at that
 * fraction, so the bright set of 4 always sits where the line crosses the middle
 * of the screen and moves as you scroll. No scroll-timeline dependency.
 */
(function () {
	const tracks = document.querySelectorAll('.snel-flow-track');
	if (!tracks.length) return;

	function update() {
		const vh     = window.innerHeight;
		const center = vh / 2;
		tracks.forEach((t) => {
			const r      = t.getBoundingClientRect();
			const snel_p = Math.max(0, Math.min(1, (center - r.top) / r.height));
			t.style.setProperty('--snel-p', snel_p.toFixed(4));

			// Exact SVG-space overlap: getPointAtLength gives pulse position in SVG
			// coords; getBBox gives chip bounds in the same coords — no conversions.
			const inView = r.top < vh && r.bottom > 0;
			const pulse  = t.querySelector('.snel-flow-pulse');
			if (!pulse) return;

			const len     = pulse.getTotalLength();
			const leadY   = pulse.getPointAtLength(Math.min(1, snel_p + 0.045) * len).y;
			const trailY  = pulse.getPointAtLength(Math.max(0, snel_p - 0.045) * len).y;

			t.querySelectorAll('.snel-proc-chip').forEach((g) => {
				const bb  = g.getBBox();
				// Active when the lit segment overlaps this chip's Y range
				const hit = inView && leadY >= bb.y && trailY <= bb.y + bb.height;
				g.classList.toggle('is-active', hit);
			});
		});
	}

	let ticking = false;
	function onScroll() {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(() => {
			update();
			ticking = false;
		});
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
	update();
})();

// Reveal each step card as it scrolls into view (fade + slide-up, once each).
(function () {
	const cards = document.querySelectorAll('.snel-proc-reveal');
	if (!cards.length) return;
	if (!('IntersectionObserver' in window)) {
		cards.forEach((c) => c.classList.add('is-in'));
		return;
	}
	const io = new IntersectionObserver(
		(entries) => {
			entries.forEach((e) => {
				if (!e.isIntersecting) return;
				e.target.classList.add('is-in');
				io.unobserve(e.target);
			});
		},
		{ threshold: 0.2, rootMargin: '0px 0px -20% 0px' }
	);
	cards.forEach((c) => io.observe(c));
})();
