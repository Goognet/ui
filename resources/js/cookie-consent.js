/**
 * Nothing is blocked before the click: the banner only records that it was seen. The
 * cookie is written from the browser and read back in Blade, which is why it has to
 * stay out of Laravel's cookie encryption — see `encryptCookies` in bootstrap/app.php.
 */
const ONE_YEAR = 60 * 60 * 24 * 365;

const HEIGHT = '--cookie-consent-height';

/**
 * On narrow screens the notice is a bar on the bottom edge and the floating WhatsApp
 * button has to sit above it. The height is published as a custom property instead of
 * hard-coded in CSS because the copy wraps to a different number of lines per width.
 */
function publishHeight(banner) {
    const write = () => document.documentElement.style.setProperty(HEIGHT, `${banner.offsetHeight}px`);

    write();

    const observer = new ResizeObserver(write);

    observer.observe(banner);

    return observer;
}

export function initCookieConsent() {
    document.querySelectorAll('[data-cookie-consent]').forEach((banner) => {
        const accept = banner.querySelector('[data-cookie-accept]');

        if (accept === null) {
            return;
        }

        const observer = publishHeight(banner);

        accept.addEventListener('click', () => {
            const secure = window.location.protocol === 'https:' ? '; Secure' : '';

            const name = banner.dataset.cookieConsent ?? '';

            /** A name carrying `;` or `=` would write cookie attributes of its own. */
            if (!/^[A-Za-z0-9_-]+$/.test(name)) {
                return;
            }

            document.cookie = `${name}=accepted; Max-Age=${ONE_YEAR}; Path=/; SameSite=Lax${secure}`;

            observer.disconnect();

            document.documentElement.style.removeProperty(HEIGHT);

            banner.remove();
        });
    });
}
