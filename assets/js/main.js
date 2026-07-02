// Global animation freeze coordinator.
// Transient UI like the mobile menu uses a `backdrop-blur` panel; blurring a
// *moving* background (canvas mesh, beams, CSS animations) forces a re-blur
// every frame and makes the open/close animation janky. Freezing all background
// motion for the duration makes the panel blur a static image = smooth.
// - CSS animations: paused via the `snel-anim-frozen` class (see theme.css).
// - Canvas meshes + SMIL beams: listen for the `snel:anim` event below.
window.snelAnim = window.snelAnim || {
    frozen: false,
    pause: function () {
        if (this.frozen) return;
        this.frozen = true;
        document.documentElement.classList.add('snel-anim-frozen');
        document.dispatchEvent(new CustomEvent('snel:anim', { detail: { frozen: true } }));
    },
    resume: function () {
        if (!this.frozen) return;
        this.frozen = false;
        document.documentElement.classList.remove('snel-anim-frozen');
        document.dispatchEvent(new CustomEvent('snel:anim', { detail: { frozen: false } }));
    }
};

(function () {
    'use strict';

    // Language Switcher Popover
    const btn = document.getElementById('snel-lang-btn');
    const popover = document.getElementById('snel-lang-popover');
    const chevron = document.getElementById('snel-lang-chevron');

    if (!btn || !popover) return;

    let isOpen = false;

    function open() {
        isOpen = true;
        popover.classList.remove('opacity-0', 'invisible', 'scale-95');
        popover.classList.add('opacity-100', 'visible', 'scale-100');
        btn.setAttribute('aria-expanded', 'true');
        chevron.classList.add('rotate-180');
    }

    function close() {
        isOpen = false;
        popover.classList.add('opacity-0', 'invisible', 'scale-95');
        popover.classList.remove('opacity-100', 'visible', 'scale-100');
        btn.setAttribute('aria-expanded', 'false');
        chevron.classList.remove('rotate-180');
    }

    btn.addEventListener('click', (e) => { e.stopPropagation(); isOpen ? close() : open(); });
    document.addEventListener('click', (e) => { if (isOpen && !popover.contains(e.target) && !btn.contains(e.target)) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen) { close(); btn.focus(); } });
})();

(function () {
    'use strict';

    // Mobile Menu
    const toggle = document.getElementById('snel-mobile-toggle');
    const menu = document.getElementById('snel-mobile-menu');
    const header = document.getElementById('snel-header');

    if (!toggle || !menu) return;

    const iconMenu = toggle.querySelector('.snel-icon-menu');
    const iconClose = toggle.querySelector('.snel-icon-close');
    const openClasses = ['visible', 'max-h-[500px]', 'opacity-100', 'border-white/30'];
    const closedClasses = ['invisible', 'max-h-0', 'opacity-0', 'border-transparent'];

    let isOpen = false;

    function open() {
        isOpen = true;
        window.snelAnim.pause(); // still the background so the blurred panel opens smoothly
        menu.classList.remove(...closedClasses);
        menu.classList.add(...openClasses);
        toggle.setAttribute('aria-expanded', 'true');
        if (iconMenu) iconMenu.classList.add('hidden');
        if (iconClose) iconClose.classList.remove('hidden');
    }

    function close() {
        isOpen = false;
        menu.classList.add(...closedClasses);
        menu.classList.remove(...openClasses);
        toggle.setAttribute('aria-expanded', 'false');
        if (iconMenu) iconMenu.classList.remove('hidden');
        if (iconClose) iconClose.classList.add('hidden');
        // Keep the background frozen until the close transition (300ms) finishes.
        setTimeout(function () { if (!isOpen) window.snelAnim.resume(); }, 350);
    }

    toggle.addEventListener('click', (e) => { e.stopPropagation(); isOpen ? close() : open(); });
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) close(); });
    document.addEventListener('click', (e) => { if (isOpen && header && !header.contains(e.target)) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen) close(); });
})();

(function () {
    'use strict';

    // Pause beam SVG animations when off-screen (or when globally frozen),
    // resume when in view.
    const svgs = document.querySelectorAll('.snel-beams');
    if (!svgs.length || !('IntersectionObserver' in window)) return;

    let frozen = false;
    const visible = new Map();

    function apply(svg) {
        (visible.get(svg) && !frozen) ? svg.unpauseAnimations() : svg.pauseAnimations();
    }

    svgs.forEach((svg) => {
        visible.set(svg, false);
        svg.pauseAnimations();
        const io = new IntersectionObserver(
            ([entry]) => { visible.set(svg, entry.isIntersecting); apply(svg); },
            { rootMargin: '100px' }
        );
        io.observe(svg);
    });

    document.addEventListener('snel:anim', (e) => {
        frozen = !!(e.detail && e.detail.frozen);
        svgs.forEach(apply);
    });
})();
