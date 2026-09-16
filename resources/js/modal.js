/**
 * Wires the dialog-based modals. Opening goes through `showModal()`, which is
 * what brings the focus trap, the Esc key and the inert background along, so the
 * script itself only routes clicks and restores page scrolling.
 */

/** Triggers point at a modal by name: `data-modal-open="orcamento"`. */
function modalNamed(name) {
    const modal = document.getElementById(name);

    return modal instanceof HTMLDialogElement ? modal : null;
}

/**
 * The dialog fills the viewport as a positioned box, so a click that lands on
 * the element itself — rather than on the panel inside it — is a backdrop click.
 */
function clickedBackdrop(modal, event) {
    return event.target === modal;
}

function open(modal) {
    modal.showModal();

    /** The background is inert but still scrollable, which drags the page behind the modal. */
    document.body.style.overflow = 'hidden';
}

function close(modal) {
    modal.close();
}

export function initModals() {
    if (document.querySelector('[data-modal]') === null) {
        return;
    }

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-modal-open]');

        if (opener !== null) {
            const modal = modalNamed(opener.dataset.modalOpen);

            if (modal !== null) {
                event.preventDefault();
                open(modal);
            }

            return;
        }

        const closer = event.target.closest('[data-modal-close]');

        if (closer !== null) {
            const modal = closer.closest('[data-modal]');

            if (modal !== null) {
                event.preventDefault();
                close(modal);
            }
        }
    });

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        const isStatic = modal.dataset.modalStatic !== undefined;

        modal.addEventListener('click', (event) => {
            if (!isStatic && clickedBackdrop(modal, event)) {
                close(modal);
            }
        });

        /** `cancel` covers the Esc key, the one close path that never passes through a click. */
        modal.addEventListener('cancel', (event) => {
            if (isStatic) {
                event.preventDefault();
            }
        });

        /** Scrolling comes back only once no modal is left open. */
        modal.addEventListener('close', () => {
            if (document.querySelector('[data-modal][open]') === null) {
                document.body.style.overflow = '';
            }
        });
    });
}
