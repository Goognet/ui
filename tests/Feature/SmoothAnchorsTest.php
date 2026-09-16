<?php

declare(strict_types = 1);

/**
 * The easing on same-page links, guarded at the source. The behaviour itself lives in the
 * browser, so what a test can hold is the rule the module declares.
 */
function smoothAnchors(): string
{
    return file_get_contents(__DIR__ . '/../../resources/js/smooth-anchors.js');
}

it('stops easing once the journey is longer than a few screens', function (): void {
    /**
     * A browser cancels a programmatic smooth scroll the moment the reader touches the wheel,
     * and a long trip gives them time to do it. Measured on the component catalogue, 56,000px
     * tall: a scroll of a single pixel 400ms into the journey left the page 29,287px short of
     * the section that had been clicked — around thirty sections past it.
     *
     * With the cap, the same interruption leaves 23 of the 24 entries landing on target.
     */
    expect(smoothAnchors())
        ->toContain('MAX_SMOOTH_SCREENS')
        ->toMatch('/distanceTo\(target\) > window\.innerHeight \* MAX_SMOOTH_SCREENS/')
        ->toContain("? 'instant' : 'smooth'");
});

it('keeps the cap within a few screens', function (): void {
    /** Past three screens nobody reads what flies by, so the easing only risks the destination. */
    preg_match('/const MAX_SMOOTH_SCREENS = (\d+)/', smoothAnchors(), $matches);

    expect($matches[1] ?? null)->not->toBeNull()
        ->and((int) $matches[1])->toBeGreaterThanOrEqual(2)
        ->and((int) $matches[1])->toBeLessThanOrEqual(5);
});

it('measures the distance from the target, not from the document', function (): void {
    /** The root goes to zero, so its distance is the scroll position; a section's is its own top. */
    expect(smoothAnchors())
        ->toContain('target === document.documentElement')
        ->toContain('Math.abs(target.getBoundingClientRect().top)');
});
