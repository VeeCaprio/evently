import './bootstrap';

const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
    } else {
        callback();
    }
};

onReady(() => {
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuButton && mobileMenu) {
        const setMenuState = (open) => {
            mobileMenu.classList.toggle('hidden', !open);
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        menuButton.addEventListener('click', () => {
            setMenuState(mobileMenu.classList.contains('hidden'));
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMenuState(false));
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                setMenuState(false);
            }
        });
    }

    const revealItems = document.querySelectorAll('.reveal');

    if (revealItems.length > 0) {
        if (!('IntersectionObserver' in window)) {
            revealItems.forEach((item) => item.classList.add('is-visible'));
        } else {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
            );

            revealItems.forEach((item) => observer.observe(item));
        }
    }

    document.querySelectorAll('[data-copy-link]').forEach((button) => {
        button.addEventListener('click', async () => {
            const label = button.querySelector('span') ?? button;
            const originalLabel = label.textContent.trim();

            const showLabel = (text) => {
                label.textContent = text;
                window.setTimeout(() => {
                    label.textContent = originalLabel;
                }, 2000);
            };

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(window.location.href);
                } else {
                    const field = document.createElement('textarea');
                    field.value = window.location.href;
                    field.setAttribute('readonly', '');
                    field.style.position = 'absolute';
                    field.style.left = '-9999px';
                    document.body.appendChild(field);
                    field.select();
                    document.execCommand('copy');
                    field.remove();
                }

                showLabel('Link copied');
            } catch (error) {
                showLabel('Copy failed');
            }
        });
    });

    window.__eventlyReady = true;
});
