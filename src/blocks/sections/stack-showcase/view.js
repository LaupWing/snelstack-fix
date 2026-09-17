import { createRoot } from '@wordpress/element';

/**
 * Stack Showcase loader.
 *
 * The three.js bundle is a lazy webpack chunk. Webpack resolves chunk URLs from
 * `document.currentScript.src` at runtime ("auto" publicPath), which assumes
 * this file is still served from build/blocks/sections/stack-showcase/view.js.
 * SiteGround Optimizer concatenates every script into one file under
 * wp-content/uploads/siteground-optimizer-assets/, so that guess resolves to
 * /wp-content/<chunk>.js and 404s — the stack then never loads for any visitor
 * who gets the combined bundle. render.php prints the real build URL in
 * data-build; pin the public path to it before the first dynamic import.
 */
const BUILD_URL = document.querySelector('.snel-stack-showcase')?.dataset.build;
if (BUILD_URL) {
	__webpack_public_path__ = BUILD_URL;
}

// Cheap WebGL probe — a context we throw away immediately. Without this the
// placeholder would be swapped for a canvas that can never draw anything.
const hasWebGL = () => {
	try {
		const c = document.createElement('canvas');
		return !!(c.getContext('webgl2') || c.getContext('webgl'));
	} catch (e) {
		return false;
	}
};

document.querySelectorAll('.snel-stack-showcase').forEach((el) => {
	let slides = [];
	try {
		slides = JSON.parse(el.dataset.slides || '[]');
	} catch (e) {
		slides = [];
	}
	if (!slides.length) return;

	let root = null;
	let loading = false;
	const placeholder = el.querySelector('.snel-stack-placeholder');
	const cta = el.querySelector('.snel-stack-cta-label');
	const ctaText = cta ? cta.textContent : '';

	const setCta = (text) => {
		if (cta) cta.textContent = text;
	};

	const mount = async () => {
		if (root || loading) return;

		if (!hasWebGL()) {
			setCta(el.dataset.noWebgl || ctaText);
			return;
		}

		// Keep the placeholder on screen while the ~1 MB chunk downloads, so a
		// slow connection or a weak machine shows "loading" instead of a black
		// box. It is only swapped out once the chunk has actually resolved.
		loading = true;
		setCta(el.dataset.loadingLabel || ctaText);

		let StackShowcase;
		try {
			({ default: StackShowcase } = await import('./StackShowcase'));
		} catch (e) {
			loading = false;
			setCta(el.dataset.errorLabel || ctaText);
			return;
		}

		const container = document.createElement('div');
		container.className = 'snel-stack-canvas';
		el.appendChild(container);

		root = createRoot(container);
		root.render(<StackShowcase slides={slides} />);

		loading = false;
		if (placeholder) placeholder.hidden = true;
		setCta(ctaText);
	};

	const unmount = () => {
		if (!root) return;
		root.unmount();
		root = null;
		el.querySelector('.snel-stack-canvas')?.remove();
		if (placeholder) placeholder.hidden = false;
		setCta(ctaText);
	};

	if (placeholder) placeholder.addEventListener('click', mount);

	if ('IntersectionObserver' in window) {
		const io = new IntersectionObserver(
			([entry]) => { if (!entry.isIntersecting) unmount(); },
			{ rootMargin: '-50px' }
		);
		io.observe(el);
	}
});
