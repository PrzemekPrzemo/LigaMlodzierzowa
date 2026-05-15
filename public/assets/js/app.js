(function () {
    'use strict';

    // Mobile nav toggle
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('primary-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // View switch (rounds-table vs ranking) on /wyniki
    const vsBtns = document.querySelectorAll('.vs-btn');
    if (vsBtns.length) {
        vsBtns.forEach(b => {
            b.addEventListener('click', () => {
                vsBtns.forEach(x => x.classList.toggle('is-active', x === b));
                const target = b.dataset.view;
                document.querySelectorAll('.view').forEach(v => {
                    v.classList.toggle('is-active', v.classList.contains('view-' + target));
                });
            });
        });
    }

    // Tabs (wyniki)
    const tabs = document.querySelectorAll('.tab');
    if (tabs.length) {
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.dataset.target;
                document.querySelectorAll('.tab').forEach(t => t.classList.toggle('is-active', t === btn));
                document.querySelectorAll('.tab-panel').forEach(p => {
                    p.classList.toggle('is-active', p.id === targetId);
                });
                history.replaceState(null, '', '#' + targetId);
            });
        });
        const hash = location.hash.replace('#', '');
        if (hash) {
            const t = document.querySelector('.tab[data-target="' + hash + '"]');
            if (t) t.click();
        }
    }
})();
