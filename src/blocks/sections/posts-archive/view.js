const obs = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            setTimeout(() => entry.target.classList.add('is-in'), i * 60);
            obs.unobserve(entry.target);
        }
    });
}, { threshold: 0.08 });

document.querySelectorAll('.snel-archive-card').forEach(card => obs.observe(card));
