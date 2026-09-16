import { loadLightbox } from './lightbox';

/**
 * The width of the grey placeholder YouTube serves when a thumbnail does not exist. Measured:
 * `maxresdefault.jpg` for a video that was never uploaded in HD answers 404, but the body is a
 * valid 120x90 JPEG — so the browser decodes it and fires `load`, never `error`. Listening for
 * failure catches nothing; the size is the only signal that reaches the page.
 */
const PLACEHOLDER_WIDTH = 120;

/**
 * `maxresdefault.jpg` is the only YouTube thumbnail at 1280x720, and it exists only when the
 * upload had that resolution. The shape this replaces discovered that server side, with up to
 * four blocking `get_headers()` calls per video on every single render.
 */
function swapPlaceholder(image) {
    const fallback = image.dataset.videoPoster;

    if (!fallback || image.naturalWidth > PLACEHOLDER_WIDTH) {
        return;
    }

    /** Cleared first, so a fallback that is also missing cannot start the swap again. */
    delete image.dataset.videoPoster;

    image.src = fallback;
}

function onLoad(event) {
    const image = event.target;

    if (image instanceof HTMLImageElement && image.dataset.videoPoster) {
        swapPlaceholder(image);
    }
}

export function initVideos() {
    const posters = [...document.querySelectorAll('img[data-video-poster]')];

    if (posters.length === 0) {
        return;
    }

    /** `load` does not bubble, so the listener sits in the capture phase. */
    document.addEventListener('load', onLoad, true);

    /** A poster served from cache can finish before this runs, and then no event is coming. */
    posters.filter((image) => image.complete).forEach(swapPlaceholder);

    loadLightbox();
}
