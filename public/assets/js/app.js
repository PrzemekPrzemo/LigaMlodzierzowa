(function () {
    'use strict';

    // Welcome modal — pokazujemy się raz na 7 dni
    const WELCOME_KEY = 'liga_welcome_v1';
    const WELCOME_TTL = 7 * 24 * 60 * 60 * 1000; // 7 dni
    const modal = document.getElementById('welcome-modal');
    if (modal) {
        let dismissed = 0;
        try { dismissed = parseInt(localStorage.getItem(WELCOME_KEY) || '0', 10); } catch (_) {}
        if (!dismissed || (Date.now() - dismissed) > WELCOME_TTL) {
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            const closers = modal.querySelectorAll('[data-close]');
            const close = () => {
                modal.hidden = true;
                document.body.style.overflow = '';
                try { localStorage.setItem(WELCOME_KEY, Date.now().toString()); } catch (_) {}
            };
            closers.forEach(el => el.addEventListener('click', close));
            document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });
            // focus na przycisku OK dla a11y
            const ok = modal.querySelector('.btn-primary');
            if (ok) setTimeout(() => ok.focus(), 50);
        }
    }

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
