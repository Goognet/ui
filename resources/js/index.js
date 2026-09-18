import { initAlerts } from './alert';
import { initCarousels } from './carousel';
import { initCookieConsent } from './cookie-consent';
import { initCounters } from './counter';
import { loadLightbox } from './lightbox';
import { initMasks } from './mask';
import { initMenus } from './menu';
import { initModals } from './modal';
import { initNavbars } from './navbar';
import { initSidebars } from './sidebar';
import { initSmoothAnchors } from './smooth-anchors';
import { confirm, initToasts, toast } from './toast';
import { initVideos } from './video';

export {
    confirm,
    initAlerts,
    initCarousels,
    initCookieConsent,
    initCounters,
    initMasks,
    initMenus,
    initModals,
    initNavbars,
    initSidebars,
    initSmoothAnchors,
    initToasts,
    initVideos,
    loadLightbox,
    toast,
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
    initCounters();
    initMasks();
    initMenus();
    initNavbars();
    initModals();
    initSidebars();
    initSmoothAnchors();
    initToasts();
    initVideos();
}
