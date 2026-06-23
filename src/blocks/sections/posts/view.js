document.querySelectorAll('.snel-posts-carousel').forEach((carousel) => {
    const track     = carousel.querySelector('.snel-posts-track');
    const indicator = carousel.querySelector('.snel-posts-indicator');
    const thumb     = carousel.querySelector('.snel-posts-thumb');
    const prevBtn   = carousel.querySelector('.snel-posts-prev');
    const nextBtn   = carousel.querySelector('.snel-posts-next');
    const nav       = carousel.querySelector('.snel-posts-nav');

    if (!track) return;

    let index = 0;

    // Per-card reveal via IntersectionObserver.
    const revealObs = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                const delay = i * 80;
                setTimeout(() => entry.target.classList.add('is-in'), delay);
                revealObs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    carousel.querySelectorAll('.snel-post-reveal').forEach(card => revealObs.observe(card));

    function maxScroll() {
        return Math.max(0, track.scrollWidth - track.clientWidth);
    }

    function updateFromScroll() {
        const max     = maxScroll();
        const eps     = 2;
        const atStart = track.scrollLeft <= eps;
        const atEnd   = track.scrollLeft >= max - eps;

        if (indicator && thumb) {
            const iW = indicator.clientWidth;
            const tW = track.clientWidth;
            const cW = track.scrollWidth;
            if (iW && tW && cW) {
                const thumbW  = Math.max(28, Math.round(iW * (tW / cW)));
                const maxX    = Math.max(0, iW - thumbW);
                const ratio   = max ? Math.max(0, Math.min(1, track.scrollLeft / max)) : 0;
                thumb.style.width     = thumbW + 'px';
                thumb.style.transform = `translateX(${Math.round(maxX * ratio)}px)`;
            }
        }

        if (prevBtn) prevBtn.disabled = atStart;
        if (nextBtn) nextBtn.disabled = atEnd;

        const children = Array.from(track.children);
        if (children.length) {
            const viewLeft = track.scrollLeft;
            let best = 0, bestDist = Infinity;
            children.forEach((child, i) => {
                const dist = Math.abs(child.offsetLeft - viewLeft);
                if (dist < bestDist) { bestDist = dist; best = i; }
            });
            index = best;
        }
    }

    function goToIndex(i) {
        const children = Array.from(track.children);
        if (!children.length) return;
        const idx    = Math.max(0, Math.min(children.length - 1, i));
        const target = Math.min(maxScroll(), children[idx].offsetLeft);
        track.scrollTo({ left: target, behavior: 'smooth' });
        requestAnimationFrame(() => updateFromScroll());
    }

    function recalc() {
        const showUi = maxScroll() > 2;
        if (nav) {
            nav.style.opacity       = showUi ? '1' : '0';
            nav.style.pointerEvents = showUi ? 'auto' : 'none';
        }
        updateFromScroll();
        requestAnimationFrame(() => updateFromScroll());
    }

    track.addEventListener('scroll', updateFromScroll, { passive: true });
    window.addEventListener('resize', recalc, { passive: true });

    if (prevBtn) prevBtn.addEventListener('click', () => goToIndex(index - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goToIndex(index + 1));

    recalc();
});
