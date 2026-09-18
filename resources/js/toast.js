/**
 * Toasts and confirmations — the two things a page asks for from JavaScript, not from markup.
 *
 * SweetAlert2 is imported on the first use, so a page that never confirms anything never pays
 * for it. Text is always passed as text: `html` would let a message out of the database, or a
 * validation error, render as markup.
 */
let loading = null;

function load() {
    loading ??= import('sweetalert2');

    return loading;
}

const BASE = {
    buttonsStyling: false,
    customClass: {
        popup: 'rounded-surface shadow-surface',
        title: 'font-heading text-neutral-950',
        htmlContainer: 'text-neutral-700',
        confirmButton:
            'inline-flex h-control cursor-pointer items-center justify-center rounded-control bg-primary px-4 font-control text-sm text-neutral-950 shadow-control transition-colors hover:bg-primary-dark',
        cancelButton:
            'ms-2 inline-flex h-control cursor-pointer items-center justify-center rounded-control border border-neutral-200 bg-white px-4 font-control text-sm text-neutral-800 transition-colors hover:bg-neutral-50',
    },
};

/** Any text that reaches the dialog is text: never `html`. */
function content({ title, message }) {
    return {
        ...(title ? { title: String(title) } : {}),
        ...(message ? { text: String(message) } : {}),
    };
}

export async function toast(options = {}) {
    const { default: Swal } = await load();

    return Swal.fire({
        ...BASE,
        ...content(options),
        icon: options.icon ?? undefined,
        toast: true,
        position: options.position ?? 'top-end',
        showConfirmButton: false,
        timer: options.duration ?? 4000,
        timerProgressBar: true,
        customClass: { ...BASE.customClass, popup: 'rounded-surface shadow-surface' },
    });
}

export async function confirm(options = {}) {
    const { default: Swal } = await load();

    const result = await Swal.fire({
        ...BASE,
        ...content(options),
        icon: options.icon ?? 'warning',
        showCancelButton: true,
        confirmButtonText: options.confirmText ?? 'Confirmar',
        cancelButtonText: options.cancelText ?? 'Cancelar',
        reverseButtons: true,
        focusCancel: true,
    });

    return result.isConfirmed;
}

/**
 * A button or a link asking to be confirmed before it acts. The click is stopped, the dialog
 * answers, and only then is the original action replayed — a form is submitted, anything else
 * is clicked again with the guard lifted.
 */
function guard(event) {
    const trigger = event.target.closest('[data-confirm]');

    if (trigger === null || trigger.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    confirm({
        title: trigger.dataset.confirmTitle || 'Tem certeza?',
        message: trigger.dataset.confirm,
        confirmText: trigger.dataset.confirmAction,
        cancelText: trigger.dataset.confirmCancel,
    }).then((confirmed) => {
        if (!confirmed) {
            return;
        }

        trigger.dataset.confirmed = 'true';

        const form = trigger.closest('form');

        if (form !== null && (trigger.type === 'submit' || trigger.tagName === 'BUTTON')) {
            form.requestSubmit(trigger instanceof HTMLButtonElement ? trigger : undefined);

            return;
        }

        trigger.click();
    });
}

export function initToasts() {
    document.querySelectorAll('[data-toast]').forEach((element) => {
        const options = JSON.parse(element.dataset.toast || '{}');

        element.remove();

        toast(options);
    });

    if (document.querySelector('[data-confirm]') !== null) {
        document.addEventListener('click', guard, true);
    }
}
