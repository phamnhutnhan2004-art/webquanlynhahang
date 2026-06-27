(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const loader = document.querySelector('[data-page-loader]');
    const scrollKey = `hoa-sen-scroll:${window.location.pathname}${window.location.search}`;
    const revealSelectors = [
        '.section-pad',
        '.page-hero',
        '.about-toc',
        '.about-section',
        '.party-section',
        '.contact-panel',
        '.contact-footer',
        '.auth-shell',
        '.card',
        '.admin-card',
        '.stat-tile',
        '.party-service-card',
        '.party-combo-card',
        '.party-food-card',
        '.party-review',
        '.contact-card',
        '.contact-map-card',
        '.about-photo',
        '.about-soft-panel',
        '.party-gallery-img'
    ];

    if ('scrollRestoration' in window.history) {
        window.history.scrollRestoration = 'manual';
    }

    const getScrollPayload = () => {
        try {
            return JSON.parse(sessionStorage.getItem(scrollKey) || 'null');
        } catch (error) {
            return null;
        }
    };

    const saveScrollPosition = (source = 'manual') => {
        try {
            sessionStorage.setItem(scrollKey, JSON.stringify({
                x: window.scrollX,
                y: window.scrollY,
                source,
                createdAt: Date.now()
            }));
        } catch (error) {
            // Session storage may be unavailable in private browsing modes.
        }
    };

    const restoreScrollPosition = () => {
        const payload = getScrollPayload();

        if (! payload || Date.now() - Number(payload.createdAt || 0) > 120000) {
            return;
        }

        sessionStorage.removeItem(scrollKey);

        const x = Number(payload.x || 0);
        const y = Math.max(0, Number(payload.y || 0));
        const maxY = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        const targetY = Math.min(y, maxY);

        window.requestAnimationFrame(() => {
            window.scrollTo(x, targetY);

            window.setTimeout(() => {
                const latestMaxY = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
                window.scrollTo(x, Math.min(y, latestMaxY));
            }, 120);
        });
    };

    const showToast = (message, type = 'success') => {
        const text = (message || '').toString().trim();

        if (! text) {
            return;
        }

        const root = document.querySelector('[data-ui-toast-root]') || (() => {
            const node = document.createElement('div');
            node.className = 'ui-toast-root';
            node.setAttribute('data-ui-toast-root', '');
            document.body.appendChild(node);
            return node;
        })();

        const toast = document.createElement('div');
        toast.className = `ui-toast ${type === 'error' ? 'is-error' : ''}`;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.innerHTML = `
            <span class="ui-toast-icon" aria-hidden="true">${type === 'error' ? '!' : 'OK'}</span>
            <p class="ui-toast-message"></p>
        `;
        toast.querySelector('.ui-toast-message').textContent = text;
        root.appendChild(toast);

        window.requestAnimationFrame(() => toast.classList.add('is-visible'));
        window.setTimeout(() => {
            toast.classList.remove('is-visible');
            window.setTimeout(() => toast.remove(), 260);
        }, type === 'error' ? 5200 : 3600);
    };

    const announceFlashMessage = () => {
        const alert = document.querySelector('.alert-success, .alert-danger');

        if (! alert || alert.closest('.page-loader')) {
            return;
        }

        const text = alert.textContent.replace(/\s+/g, ' ').trim();
        showToast(text, alert.classList.contains('alert-danger') ? 'error' : 'success');
    };

    const enablePersistentForms = () => {
        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (! form.matches('form') || form.dataset.noScrollRestore === 'true') {
                return;
            }

            saveScrollPosition('form-submit');
        }, true);
    };

    const markReady = () => {
        document.body.classList.add('ui-ready');
        loader?.classList.add('is-hidden');
        window.setTimeout(() => loader?.remove(), 520);
    };

    const prepareReveal = () => {
        const items = [...document.querySelectorAll(revealSelectors.join(','))];

        items.forEach((item, index) => {
            if (item.closest('.page-loader') || item.hasAttribute('data-no-reveal')) {
                return;
            }

            if (! item.hasAttribute('data-reveal')) {
                item.setAttribute('data-reveal', index % 5 === 0 ? 'zoom' : 'up');
            }

            item.style.setProperty('--reveal-delay', `${Math.min(index % 4, 3) * 60}ms`);
        });

        if (prefersReducedMotion || ! ('IntersectionObserver' in window)) {
            items.forEach((item) => item.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, {
            threshold: .14,
            rootMargin: '0px 0px -8% 0px'
        });

        items.forEach((item) => observer.observe(item));
    };

    const isSamePageHash = (link) => {
        if (! link.hash) {
            return false;
        }

        return link.pathname === window.location.pathname && link.origin === window.location.origin;
    };

    const enablePageTransitions = () => {
        if (prefersReducedMotion) {
            return;
        }

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');

            if (! link || event.defaultPrevented) {
                return;
            }

            const href = link.getAttribute('href') || '';
            const isModifiedClick = event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
            const isExternal = link.origin !== window.location.origin;
            const isNewContext = link.target && link.target !== '_self';
            const isDownload = link.hasAttribute('download');
            const isSpecial = href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:');

            if (isModifiedClick || isExternal || isNewContext || isDownload || isSpecial || isSamePageHash(link)) {
                return;
            }

            if (document.body.classList.contains('ui-leaving')) {
                return;
            }

            event.preventDefault();
            document.body.classList.add('ui-leaving');
            window.setTimeout(() => {
                window.location.href = link.href;
            }, 240);
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        restoreScrollPosition();
        prepareReveal();
        enablePageTransitions();
        enablePersistentForms();
        announceFlashMessage();
        window.setTimeout(markReady, prefersReducedMotion ? 0 : 180);
    });

    window.addEventListener('pageshow', () => {
        restoreScrollPosition();
        document.body.classList.remove('ui-leaving');
        document.body.classList.add('ui-ready');
    });
})();
