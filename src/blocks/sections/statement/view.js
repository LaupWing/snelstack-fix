/**
 * Snel Statement — word-reveal scroll animation.
 *
 * Walks each .snel-wr-body's text nodes and wraps every word in
 * <span class="wr-word">. Inline elements (snel-accent, strong, em, etc.) are
 * preserved — words inside them stay inside their parent. IntersectionObserver
 * cascades words in (28 ms/word stagger) when the body enters the viewport and
 * out (8 ms/word) when it exits.
 */
(function () {
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	const bodies = document.querySelectorAll('.snel-wr-body');
	if (!bodies.length) return;

	function wrapWords(root) {
		const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
		const nodes = [];
		let n;
		while ((n = walker.nextNode())) nodes.push(n);

		nodes.forEach((node) => {
			const parts = node.textContent.split(/(\s+)/);
			// skip pure-whitespace nodes that would produce nothing useful
			if (parts.every((p) => /^\s*$/.test(p))) return;

			const frag = document.createDocumentFragment();
			parts.forEach((part) => {
				if (part === '' || /^\s+$/.test(part)) {
					frag.appendChild(document.createTextNode(part));
				} else {
					const span = document.createElement('span');
					span.className = 'wr-word';
					span.textContent = part;
					frag.appendChild(span);
				}
			});
			node.parentNode.replaceChild(frag, node);
		});
	}

	if (!('IntersectionObserver' in window)) {
		// Fallback: just show everything without animation.
		bodies.forEach((body) => {
			body.querySelectorAll('.wr-word').forEach((w) => w.classList.add('wr-in'));
		});
		return;
	}

	bodies.forEach((body) => {
		wrapWords(body);

		const words = Array.from(body.querySelectorAll('.wr-word'));
		if (!words.length) return;

		let timers = [];

		const io = new IntersectionObserver(
			([entry]) => {
				timers.forEach(clearTimeout);
				timers = [];

				if (entry.isIntersecting) {
					words.forEach((w, i) => {
						timers.push(
							setTimeout(() => {
								w.classList.add('wr-in');
								w.classList.remove('wr-out');
							}, i * 28)
						);
					});
				} else {
					words.forEach((w, i) => {
						timers.push(
							setTimeout(() => {
								w.classList.remove('wr-in');
								w.classList.add('wr-out');
							}, i * 8)
						);
					});
				}
			},
			{ threshold: 0.15 }
		);

		io.observe(body);
	});
})();
