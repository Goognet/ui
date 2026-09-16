/**
 * `scroll-behavior: smooth` on the root looks right until you use a wheel: the browser
 * eases *every* scroll, so each wheel tick animates to its own destination and the page
 * comes down in steps instead of tracking the input. Measured on a 400px jump: with the
 * rule the position was still easing 60ms later; without it, it arrived immediately.
 *
 * So the rule is gone and the easing lives here, on the one interaction that wants it —
 * clicking a link to somewhere on the same page.
 */
function targetOf(anchor) {
    const href = anchor.getAttribute('href');

    if (href === '#') {
        return document.documentElement;
    }

    /**
     * By id, never as a selector: `#1-introducao` is a valid id and an invalid selector, and
     * `querySelector` threw on it and took the click down with it.
     */
    try {
        return document.getElementById(decodeURIComponent(href.slice(1)));
    } catch {
        return null;
    }
}

/**
 * Past this, the easing is abandoned and the jump is instant.
 *
 * A browser cancels a programmatic smooth scroll the moment the user touches the wheel, and a
 * long journey gives them plenty of time to do it: measured on the component catalogue, which
 * is 56,000px tall, a scroll of one single pixel 400ms into the trip left the page 29,287px
 * short of the section that was clicked — some thirty sections away from it.
 *
 * Three screens is where the animation stops being useful anyway. Nobody reads what flies past
 * at that speed, so the only thing the easing still buys is the chance to lose the destination.
 */
const MAX_SMOOTH_SCREENS = 3;

function distanceTo(target) {
    return target === document.documentElement ? window.scrollY : Math.abs(target.getBoundingClientRect().top);
}

function behaviourFor(target) {
    return distanceTo(target) > window.innerHeight * MAX_SMOOTH_SCREENS ? 'instant' : 'smooth';
}

function onClick(event) {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey) {
        return;
    }

    const anchor = event.target.closest('a[href^="#"]');

    if (anchor === null) {
        return;
    }

    const target = targetOf(anchor);

    if (target === null) {
        return;
    }

    event.preventDefault();

    const behavior = behaviourFor(target);

    /** `scrollIntoView` honours `scroll-margin-top`, which is how the anchors clear the navbar. */
    target === document.documentElement
        ? window.scrollTo({ top: 0, behavior })
        : target.scrollIntoView({ behavior, block: 'start' });

    const hash = anchor.getAttribute('href');

    /** Keep the address bar truthful without letting it jump the page a second time. */
    history.pushState(null, '', hash === '#' ? location.pathname + location.search : hash);
}

export function initSmoothAnchors() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.addEventListener('click', onClick);
}
