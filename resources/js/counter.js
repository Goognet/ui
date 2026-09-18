/**
 * Counts up to the number already printed in the element, once it is on screen.
 *
 * The final value is rendered by the server, so a page without JavaScript — or a crawler —
 * reads the real number instead of a zero that never animates. The library is imported only
 * when a counter is on the page, and only when it is about to be seen.
 */
let loading = null;

function load() {
    loading ??= import('countup.js');

    return loading;
}

async function run(element) {
    const options = JSON.parse(element.dataset.counter || '{}');

    const { CountUp } = await load();

    const counter = new CountUp(element, options.value, {
        startVal: options.start ?? 0,
        duration: options.duration ?? 2,
        decimalPlaces: options.decimals ?? 0,
        separator: options.separator ?? '.',
        decimal: options.decimal ?? ',',
        prefix: options.prefix ?? '',
        suffix: options.suffix ?? '',
        useEasing: true,
    });

    if (!counter.error) {
        counter.start();
    }
}

export function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    if (counters.length === 0) {
        return;
    }

    /** Someone who asked for less motion gets the number, not the count. */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                /** Once: a counter that restarts every time it scrolls back reads as a glitch. */
                observer.unobserve(entry.target);

                run(entry.target);
            });
        },
        { rootMargin: '0px 0px -10% 0px' },
    );

    counters.forEach((counter) => observer.observe(counter));
}
