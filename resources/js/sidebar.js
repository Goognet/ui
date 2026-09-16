/**
 * Marks which entry of a `<x-ui.sidebar>` the reader is on.
 *
 * Neither rail this replaces had one: the border only answered the pointer, so a long page
 * told you what was on it and never where you were in it.
 */
const CURRENT = 'data-current';

/**
 * How long a click keeps its own entry lit before the scroll position takes over again, when
 * the browser gives no `scrollend`. Measured on the privacy page, a smooth scroll from the top
 * to the last section settles at about 1.6s.
 */
const CLICK_HOLD = 2000;

/**
 * Where a section comes to rest when its own anchor is clicked. Pages park headings below the
 * sticky bar with `scroll-margin-top`, so that is the line to compare against — measured, the
 * privacy page parks them at 113px while a hand-picked 104px never let the section reach it,
 * and every entry lit one behind the reader.
 */
function restingLine(section) {
    const margin = parseFloat(getComputedStyle(section).scrollMarginTop) || 0;

    if (margin > 0) {
        /** A pixel of slack: the rect lands on fractional values at some zoom levels. */
        return margin + 1;
    }

    const navbar = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--navbar-height')) || 0;

    return navbar + 24;
}

function targetsOf(nav) {
    return [...nav.querySelectorAll('a[href^="#"]')]
        .map((link) => ({ link, section: document.getElementById(decodeURIComponent(link.hash.slice(1))) }))
        .filter((pair) => pair.section !== null)
        .map((pair) => ({ ...pair, line: restingLine(pair.section) }));
}

/**
 * Keeps the marked entry inside the rail's own scroll box. The rail caps at the screen and
 * scrolls internally once the list outgrows it, so on a long index the current entry can sit
 * out of sight in a rail that is right there on screen.
 *
 * Scrolled by hand rather than with `scrollIntoView`: that one walks up the ancestors and would
 * drag the page along with it, fighting the very scroll that triggered this.
 */
function reveal(nav, link) {
    if (nav.scrollHeight <= nav.clientHeight) {
        return;
    }

    const top = link.offsetTop - nav.offsetTop;

    const bottom = top + link.offsetHeight;

    if (top < nav.scrollTop) {
        nav.scrollTop = top;

        return;
    }

    if (bottom > nav.scrollTop + nav.clientHeight) {
        nav.scrollTop = bottom - nav.clientHeight;
    }
}

function apply(nav, targets, current) {
    if (current !== undefined && current !== null) {
        reveal(nav, current.link);
    }

    targets.forEach(({ link }) => {
        const isCurrent = link === current?.link;

        link.toggleAttribute(CURRENT, isCurrent);

        /** `location` and not `true`: the entry points at a place in this page, not at a page. */
        if (isCurrent) {
            link.setAttribute('aria-current', 'location');
        } else {
            link.removeAttribute('aria-current');
        }
    });
}

function currentOf(targets) {
    /**
     * The last section that has reached its resting place, which is what "I am reading this
     * one" means — not "any part of it is on screen", because three sections fit on a screen
     * at once and the highlight would flicker between them. Before the reader has scrolled
     * past anything the first entry holds it, so the rail is never blank.
     */
    let current = targets[0];

    for (const pair of targets) {
        if (pair.section.getBoundingClientRect().top <= pair.line) {
            current = pair;
        }
    }

    /** The foot of the page cannot scroll further, so the last entry owns it. */
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 2) {
        current = targets.at(-1) ?? current;
    }

    return current;
}

function watch(nav) {
    const targets = targetsOf(nav);

    if (targets.length === 0) {
        return;
    }

    /**
     * A click lights its own entry at once and holds it until the page stops moving. Without
     * the hold, the smooth scroll walks the mark through every section on the way down and the
     * entry that was clicked only lights up a second and a half later — which reads as the rail
     * marking the wrong item.
     */
    let held = null;

    let release = null;

    let queued = false;

    const schedule = () => {
        if (queued || held !== null) {
            return;
        }

        queued = true;

        /** One read per frame: every entry measures a rect, and scroll fires far faster than that. */
        requestAnimationFrame(() => {
            queued = false;
            apply(nav, targets, currentOf(targets));
        });
    };

    const letGo = () => {
        clearTimeout(release);
        held = null;
        schedule();
    };

    nav.addEventListener('click', (event) => {
        const pair = targets.find(({ link }) => link === event.target.closest('a'));

        if (pair === undefined) {
            return;
        }

        held = pair;
        apply(nav, targets, pair);

        clearTimeout(release);

        /** `scrollend` is the precise signal; the timer covers a browser that does not fire it. */
        release = setTimeout(letGo, CLICK_HOLD);

        window.addEventListener('scrollend', letGo, { once: true });
    });

    apply(nav, targets, currentOf(targets));

    window.addEventListener('scroll', schedule, { passive: true });

    /** The bar's height, and with it every resting line, depends on the viewport. */
    window.addEventListener(
        'resize',
        () => {
            targets.forEach((pair) => {
                pair.line = restingLine(pair.section);
            });

            schedule();
        },
        { passive: true },
    );
}

export function initSidebars() {
    document.querySelectorAll('[data-sidebar]').forEach(watch);
}
