/**
 * Dismissible alerts. One listener on the document, so an alert added to the page
 * after load is dismissed by the same code.
 */
export function initAlerts() {
    if (document.querySelector('[data-alert]') === null) {
        return;
    }

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-alert-dismiss]');

        if (button === null) {
            return;
        }

        button.closest('[data-alert]')?.remove();
    });
}
