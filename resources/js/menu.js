/**
 * Drives every <x-ui.menu> on the page: the mobile drawer and the submenu
 * dropdowns. State lives in a `data-state` attribute so Tailwind drives all the
 * visuals; this file only flips that attribute and keeps ARIA in sync.
 */

const FOCUSABLE =
    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

const DESKTOP_QUERY = '(min-width: 64rem)';

function setState(element, isOpen) {
    if (!element) {
        return;
    }

    element.dataset.state = isOpen ? 'open' : 'closed';
}

function panelOf(trigger) {
    const id = trigger.getAttribute('aria-controls');

    return id ? document.getElementById(id) : null;
}

function isOpen(trigger) {
    return trigger.getAttribute('aria-expanded') === 'true';
}

function toggleTrigger(trigger, open) {
    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    setState(trigger, open);
    setState(panelOf(trigger), open);
}

function closeDropdowns(root = document) {
    root.querySelectorAll('[data-menu-dropdown]').forEach((trigger) => {
        toggleTrigger(trigger, false);
    });
}

function drawerOf(element) {
    const menu = element.closest('[data-menu]');

    return menu
        ? {
              menu,
              toggle: menu.querySelector('[data-menu-toggle]'),
              panel: menu.querySelector('[data-menu-panel]'),
              overlay: menu.querySelector('[data-menu-overlay]'),
          }
        : null;
}

function openDrawer(parts) {
    toggleTrigger(parts.toggle, true);
    setState(parts.overlay, true);

    document.body.classList.add('overflow-hidden');

    parts.panel.querySelector(FOCUSABLE)?.focus();
}

function closeDrawer(parts, { restoreFocus = true } = {}) {
    if (!parts?.toggle || !isOpen(parts.toggle)) {
        return;
    }

    toggleTrigger(parts.toggle, false);
    setState(parts.overlay, false);
    closeDropdowns(parts.panel);

    document.body.classList.remove('overflow-hidden');

    if (restoreFocus) {
        parts.toggle.focus();
    }
}

function closeAllDrawers(options) {
    document.querySelectorAll('[data-menu]').forEach((menu) => {
        closeDrawer(drawerOf(menu), options);
    });
}

/** Keeps focus inside the open drawer, the way a modal dialog must. */
function trapFocus(event, panel) {
    const focusable = [...panel.querySelectorAll(FOCUSABLE)];

    if (focusable.length === 0) {
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();

        return;
    }

    if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

function onClick(event) {
    const dropdown = event.target.closest('[data-menu-dropdown]');

    if (dropdown) {
        const shouldOpen = !isOpen(dropdown);

        closeDropdowns(dropdown.closest('[data-menu]'));
        toggleTrigger(dropdown, shouldOpen);

        return;
    }

    const toggle = event.target.closest('[data-menu-toggle]');

    if (toggle) {
        const parts = drawerOf(toggle);

        isOpen(toggle) ? closeDrawer(parts) : openDrawer(parts);

        return;
    }

    if (event.target.closest('[data-menu-close], [data-menu-overlay]')) {
        closeDrawer(drawerOf(event.target));

        return;
    }

    if (!event.target.closest('[data-menu]')) {
        closeDropdowns();
    }
}

/**
 * Arrow keys inside a dropdown. Tab alone reaches every item, but it also walks straight
 * out of the panel and on down the page; someone who opened a menu expects the arrows to
 * stay in it. Down from a closed trigger opens it and lands on the first entry, which is
 * what a menu button does everywhere else.
 */
/**
 * Focus lands only once the panel has finished opening. The panel fades in from
 * `visibility: hidden`, and a `focus()` call placed anywhere inside that transition is
 * accepted and then dropped — the element is found, the call runs, and nothing moves.
 * `transitionend` is the honest signal; the timeout covers the case where the browser
 * skips the transition entirely, as it does under reduced motion.
 */
function focusFirstItem(panel) {
    if (!panel) {
        return;
    }

    let done = false;

    const land = () => {
        if (done) {
            return;
        }

        done = true;
        panel.removeEventListener('transitionend', land);
        panel.querySelector(FOCUSABLE)?.focus();
    };

    panel.addEventListener('transitionend', land);
    setTimeout(land, 400);
}

function onArrow(event) {
    const trigger = event.target.closest('[data-menu-dropdown]');

    if (trigger !== null) {
        if (event.key !== 'ArrowDown') {
            return false;
        }

        event.preventDefault();

        if (!isOpen(trigger)) {
            closeDropdowns(trigger.closest('[data-menu]'));
            toggleTrigger(trigger, true);
        }

        focusFirstItem(panelOf(trigger));

        return true;
    }

    const panel = event.target.closest('[data-menu-dropdown-panel][data-state="open"]');

    if (panel === null) {
        return false;
    }

    const items = [...panel.querySelectorAll(FOCUSABLE)];
    const index = items.indexOf(event.target);

    if (index === -1) {
        return false;
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();

        const step = event.key === 'ArrowDown' ? 1 : -1;

        /** Wrapping is what makes a short list feel like a loop instead of a dead end. */
        items[(index + step + items.length) % items.length].focus();

        return true;
    }

    if (event.key === 'Home' || event.key === 'End') {
        event.preventDefault();

        (event.key === 'Home' ? items[0] : items[items.length - 1]).focus();

        return true;
    }

    return false;
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        /** Focus goes back to the trigger that opened the panel, not to the top of the page. */
        const open = document.querySelector('[data-menu-dropdown][data-state="open"]');

        closeDropdowns();
        closeAllDrawers();

        open?.focus();

        return;
    }

    if (onArrow(event)) {
        return;
    }

    if (event.key !== 'Tab') {
        return;
    }

    const openPanel = document.querySelector('[data-menu-panel][data-state="open"]');

    if (openPanel) {
        trapFocus(event, openPanel);
    }
}

export function initMenus() {
    if (document.querySelector('[data-menu]') === null) {
        return;
    }

    document.addEventListener('click', onClick);
    document.addEventListener('keydown', onKeydown);

    /** A drawer left open while the viewport grows would keep the body locked. */
    window.matchMedia(DESKTOP_QUERY).addEventListener('change', (event) => {
        if (event.matches) {
            closeAllDrawers({ restoreFocus: false });
        }
    });
}
