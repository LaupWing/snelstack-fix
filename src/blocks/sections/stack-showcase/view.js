import { createRoot } from '@wordpress/element';

document.querySelectorAll('.snel-stack-showcase').forEach((el) => {
	let slides = [];
	try {
		slides = JSON.parse(el.dataset.slides || '[]');
	} catch (e) {
		slides = [];
	}
	if (!slides.length) return;

	let root = null;
	const placeholder = el.querySelector('.snel-stack-placeholder');

	const mount = async () => {
		if (root) return;
		if (placeholder) placeholder.hidden = true;

		const container = document.createElement('div');
		container.className = 'snel-stack-canvas';
		el.appendChild(container);

		const { default: StackShowcase } = await import('./StackShowcase');
		root = createRoot(container);
		root.render(<StackShowcase slides={slides} />);
	};

	const unmount = () => {
		if (!root) return;
		root.unmount();
		root = null;
		el.querySelector('.snel-stack-canvas')?.remove();
		if (placeholder) placeholder.hidden = false;
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
