/**
 * fsLightbox scans the document the moment it is imported and keeps the anchors it found, in
 * the order it found them. That single scan is why the import is shared: it has to happen once,
 * before anything moves the anchors around, and a second import would not re-scan.
 *
 * It used to live inside carousel.js and only looked at `[data-carousel]` roots, so a standalone
 * `<x-ui.video>` never loaded the library at all — its link simply navigated to YouTube.
 */
let loading = null;

export function loadLightbox() {
    if (loading !== null) {
        return loading;
    }

    if (document.querySelector('a[data-fslightbox]') === null) {
        return Promise.resolve();
    }

    loading = import('fslightbox');

    return loading;
}
