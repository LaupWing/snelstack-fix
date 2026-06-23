/**
 * Stack Showcase — frontend view script.
 *
 * Three.js is heavy. We lazy-load it only when the element is about to enter
 * the viewport (IntersectionObserver + dynamic import). The PHP render already
 * outputs a spinner placeholder so the layout doesn't jump.
 *
 * @package Snel
 */
import { createRoot } from '@wordpress/element';

document.querySelectorAll('.snel-stack-showcase').forEach((el) => {
	let slides = [];
	try {
		slides = JSON.parse(el.dataset.slides || '[]');
	} catch (e) {
		slides = [];
	}
	if (!slides.length) return;

	const mount = async () => {
		const { default: StackShowcase } = await import('./StackShowcase');
		el.innerHTML = '';
		createRoot(el).render(<StackShowcase slides={slides} />);
	};

	if ('IntersectionObserver' in window) {
		const obs = new IntersectionObserver(
			([entry]) => {
				if (entry.isIntersecting) {
					obs.disconnect();
					mount();
				}
			},
			{ rootMargin: '200px' }
		);
		obs.observe(el);
	} else {
		mount();
	}
});
