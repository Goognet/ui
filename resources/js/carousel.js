/**
 * Wires every `<x-ui.carousel>` on the page. The Blade component writes its whole
 * configuration into `data-carousel`, so this script only turns that description
 * into Swiper options — scoped per instance, never by a page-wide selector.
 */

import Swiper from 'swiper';
import { loadLightbox } from './lightbox';
import { A11y, Autoplay, Keyboard, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

/** Autoplay that cannot be stopped is hostile to anyone who asked for less motion. */
function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function readConfig(root) {
    try {
        return JSON.parse(root.dataset.carousel);
    } catch {
        return null;
    }
}

/**
 * The widest `slidesPerView` any breakpoint asks for. Looping with fewer slides than
 * that leaves Swiper duplicating nothing and the track jumps, so loop stays off.
 */
function widestView(config) {
    return Object.values(config.breakpoints ?? {}).reduce(
        (widest, at) => Math.max(widest, at.slidesPerView ?? 0),
        config.slidesPerView ?? 1,
    );
}

/**
 * Swiper needs at least `slidesPerView + slidesPerGroup` slides to loop (swiper-core,
 * `loopFix`); below that it logs a warning and runs without the loop anyway. So the count is
 * checked even when `loop` is forced: `true` means loop whenever it can work, `false` never.
 */
export function shouldLoop(config, slideCount) {
    if (config.loop === false) {
        return false;
    }

    return slideCount >= widestView(config) + 1;
}

function options(root, config, slideCount) {
    const autoplay =
        config.autoplay !== false && !prefersReducedMotion()
            ? {
                  delay: config.autoplay,
                  disableOnInteraction: false,

                  /** Swiper's own hover pause. A manual mouseover listener also fires for
                   *  every child the pointer crosses, which thrashes autoplay. */
                  pauseOnMouseEnter: true,
              }
            : false;

    return {
        modules: [A11y, Autoplay, Keyboard, Navigation, Pagination],
        slidesPerView: config.slidesPerView,
        spaceBetween: config.spaceBetween,
        breakpoints: config.breakpoints,
        loop: shouldLoop(config, slideCount),
        grabCursor: true,
        autoplay,

        /**
         * Swiper's own stylesheet does the work once the class is on: the wrapper aligns to
         * the start and transitions its height. The slide keeps `h-auto` either way, so a
         * carousel that does not ask for this still stretches every slide to the tallest.
         */
        autoHeight: config.autoHeight === true,

        /** Swiper 12 has no `lazy` option: images load natively through `loading="lazy"`,
         *  and this only widens how far ahead Swiper warms them up. */
        lazyPreloadPrevNext: 1,

        keyboard: { enabled: true, onlyInViewport: true },

        pagination: config.pagination
            ? {
                  el: root.querySelector('.swiper-pagination'),
                  clickable: true,

                  /** Off by default. Swiper scales the bullet itself, which is the hit target —
                   *  `app.css` moves that scale onto the dot so the target stays 24px. */
                  dynamicBullets: config.dynamicBullets === true,
              }
            : false,

        navigation: config.navigation
            ? {
                  prevEl: root.querySelector('.swiper-button-prev'),
                  nextEl: root.querySelector('.swiper-button-next'),
              }
            : false,
    };
}

export async function initCarousels() {
    const roots = [...document.querySelectorAll('[data-carousel]')];

    /**
     * Before Swiper, always: `loop` moves the slide elements around, and fsLightbox keeps
     * whatever order it saw at import. Scanning afterwards would read the shuffled DOM and
     * the gallery would open out of order.
     */
    await loadLightbox();

    roots.forEach((root) => {
        const config = readConfig(root);

        const container = root.querySelector('.swiper');

        if (config === null || container === null) {
            return;
        }

        new Swiper(container, options(root, config, container.querySelectorAll('.swiper-slide').length));
    });
}
