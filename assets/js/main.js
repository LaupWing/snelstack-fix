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
    }

    toggle.addEventListener('click', (e) => { e.stopPropagation(); isOpen ? close() : open(); });
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) close(); });
    document.addEventListener('click', (e) => { if (isOpen && header && !header.contains(e.target)) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen) close(); });
})();
