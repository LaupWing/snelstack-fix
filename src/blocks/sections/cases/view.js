if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
	document.querySelectorAll('.snel-case-card').forEach((el) => el.classList.add('is-in'));
} else {
	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-in');
					observer.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.12 }
	);
	document.querySelectorAll('.snel-case-card').forEach((el) => observer.observe(el));
}
