import { initAlerts } from './alert';
import { initCarousels } from './carousel';
import { initCookieConsent } from './cookie-consent';
import { loadLightbox } from './lightbox';
import { initMenus } from './menu';
import { initModals } from './modal';
import { initNavbars } from './navbar';
import { initSidebars } from './sidebar';
import { initSmoothAnchors } from './smooth-anchors';
import { initVideos } from './video';

export {
    initAlerts,
    initCarousels,
    initCookieConsent,
    initMenus,
    initModals,
    initNavbars,
    initSidebars,
    initSmoothAnchors,
    initVideos,
    loadLightbox,
};

/**
 * Starts every component on the page. Each part looks for its own markup and does nothing
 * without it, so a page with no carousel pays nothing for Swiper beyond the import.
 */
export function initUi() {
    /** Before the carousels: fsLightbox keeps the anchor order it saw, and `loop` moves slides. */
    loadLightbox();

    initAlerts();
    initCarousels();
    initCookieConsent();
    initMenus();
    initNavbars();
    initModals();
    initSidebars();
    initSmoothAnchors();
    initVideos();
}
