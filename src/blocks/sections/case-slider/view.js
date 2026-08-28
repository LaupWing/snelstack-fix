document.querySelectorAll('.snel-case-slider').forEach((slider) => {
    const track = slider.querySelector('.snel-case-slider-track');
    if (!track) return;

    const prev      = slider.querySelector('.snel-case-slider-prev');
    const next      = slider.querySelector('.snel-case-slider-next');
    const indicator = slider.querySelector('.snel-case-slider-indicator');
    const bar       = slider.querySelector('.snel-case-slider-bar');

    const index = () => Math.round(track.scrollLeft / track.clientWidth);
    const goTo  = (i) => track.scrollTo({ left: i * track.clientWidth, behavior: 'smooth' });

    function update() {
        const i    = index();
        const last = track.children.length - 1;
        if (prev) prev.disabled = i <= 0;
        if (next) next.disabled = i >= last;

        if (indicator && bar) {
            const max = Math.max(0, track.scrollWidth - track.clientWidth);
            const iW  = indicator.clientWidth;
            if (iW && track.scrollWidth) {
                const barW  = Math.max(12, Math.round(iW * (track.clientWidth / track.scrollWidth)));
                const maxX  = Math.max(0, iW - barW);
                const ratio = max ? Math.max(0, Math.min(1, track.scrollLeft / max)) : 0;
                bar.style.width     = barW + 'px';
                bar.style.transform = `translateX(${Math.round(maxX * ratio)}px)`;
            }
        }
    }

    prev?.addEventListener('click', () => goTo(index() - 1));
    next?.addEventListener('click', () => goTo(index() + 1));

    track.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
    window.addEventListener('resize', update);
    update();
});
