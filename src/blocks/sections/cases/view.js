console.log('[snel] cases/view.js START', performance.now().toFixed(1) + 'ms');
performance.mark('snel-cases-start');
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
performance.mark('snel-cases-end');
console.log('[snel] cases/view.js END', performance.now().toFixed(1) + 'ms');
