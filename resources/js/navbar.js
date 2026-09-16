/**
 * Drives the scroll-reactive navbars. The script only writes data attributes —
 * `data-scrolled` for the transparent-to-solid swap and `data-hidden` for the
 * hide-on-scroll-down behaviour — and Tailwind does the rest.
 */

/** Past this the overlay bar turns solid. */
const SOLID_AT = 8;

/** Below this the bar always stays put, so it never flickers at the top. */
const HIDE_AFTER = 96;

/**
 * Mobile browsers retract their URL bar on the way down and slide it back on
 * the way up. Sliding it back shrinks the viewport, which nudges scrollY
 * *upwards* while the finger moves down the screen. Without a tolerance those
 * few pixels read as "scrolling down" and the bar stays hidden until the top of
 * the page. Direction only changes once the reading moves this far.
 */
const DIRECTION_TOLERANCE = 8;

let lastY = 0;

/**
 * Mobile browser chrome sliding in or out resizes the viewport, and the
 * document shifts with it. That shift arrives as a scroll event the user never
 * made — on iOS it can be the full height of the URL bar, far past any
 * tolerance. Frames where the height moved are resynced instead of steered.
 */
let lastHeight = 0;

let ticking = false;

function update() {
    ticking = false;

    const y = Math.max(window.scrollY, 0);
    const height = window.innerHeight;
    const chromeMoved = height !== lastHeight;
    const delta = y - lastY;
    const movedEnough = !chromeMoved && Math.abs(delta) >= DIRECTION_TOLERANCE;

    document.querySelectorAll('[data-navbar]').forEach((navbar) => {
        navbar.dataset.scrolled = y > SOLID_AT ? 'true' : 'false';

        if (navbar.dataset.autoHide !== 'true') {
            return;
        }

        /** Hiding the bar with a panel open would take the close button with it. */
        const hasOpenPanel = navbar.querySelector('[data-state="open"]') !== null;

        if (y <= HIDE_AFTER || hasOpenPanel) {
            navbar.dataset.hidden = 'false';

            return;
        }

        if (movedEnough) {
            navbar.dataset.hidden = delta > 0 ? 'true' : 'false';
        }
    });

    /** Holding the old reading lets small nudges accumulate into a real direction. */
    if (movedEnough || chromeMoved) {
        lastY = y;
    }

    lastHeight = height;
}

function onScroll() {
    if (ticking) {
        return;
    }

    ticking = true;

    window.requestAnimationFrame(update);
}

/**
 * Anything else that sticks — a page's own index rail, an anchor target — has to clear the
 * bar, and the bar's height depends on what the site put in it: the info strip is optional
 * and the logo sets the rest. Publishing the measured height keeps those offsets honest
 * instead of hard-coding a number that is wrong on the next site.
 */
function publishHeight(navbar) {
    const write = () => document.documentElement.style.setProperty('--navbar-height', `${navbar.offsetHeight}px`);

    write();

    new ResizeObserver(write).observe(navbar);
}

export function initNavbars() {
    const navbars = document.querySelectorAll('[data-navbar]');

    if (navbars.length === 0) {
        return;
    }

    /** The first bar is the page's own: a second one would be inside a panel or a demo. */
    publishHeight(navbars[0]);

    lastY = Math.max(window.scrollY, 0);
    lastHeight = window.innerHeight;

    update();

    window.addEventListener('scroll', onScroll, { passive: true });
}
