<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The brand colours, checked at the source rather than trusted.
 *
 * What these prove: the semantic tokens derive from the numbered scale instead of naming a
 * palette a second time, and the ink tokens force a lightness dark enough to read on white.
 *
 * What they do not prove: the exact ratio a browser paints. Measuring that needs a browser to
 * resolve `oklch(from …)`, and it was measured there — lime 6.85, purple 7.98, and across six
 * very different brands the worst was cyan at 6.33. The threshold below carries that margin.
 */
function theme(): string
{
    return file_get_contents(__DIR__ . '/../../resources/css/ui.css');
}

it('derives the semantic tokens from the scale, not from the palette again', function (string $brand): void {
    /**
     * `--color-primary: var(--color-lime-500)` and `--color-primary-700: var(--color-lime-700)`
     * both pointed at the palette in parallel. A site that repainted the first kept the second:
     * the button turned blue and the link's hover stayed green.
     */
    expect(theme())->toContain("--color-{$brand}: var(--color-{$brand}-500);");
})->with(['primary', 'secondary']);

it('measures the dark and light pair off the brand colour, not off a step of the scale', function (string $brand): void {
    /**
     * `--color-primary-dark: var(--color-primary-600)` only holds while the brand sits on the
     * 500. A site that pointed the brand at cyan-700 kept the 600 as its "dark" — a lighter
     * colour — and the button brightened on hover. Lightness is measured from the brand now.
     */
    expect(theme())
        ->toMatch('/--color-' . $brand . '-dark:\s*oklch\(from var\(--color-' . $brand . '\) calc\(l - /')
        ->toMatch('/--color-' . $brand . '-light:\s*oklch\(from var\(--color-' . $brand . '\) calc\(l \+ /');
})->with(['primary', 'secondary']);

it('lets the brand pick the text that covers it', function (string $brand): void {
    /**
     * No single colour reads on every fill: black over lime measures 10.77:1 and white 1.96:1,
     * and over cyan-700 they swap to 3.81:1 and 5.28:1. The token switches on the fill's
     * lightness, so the components can stop naming a colour they cannot know.
     */
    expect(theme())->toMatch(
        '/--color-' . $brand . '-contrast:\s*oklch\(from var\(--color-' . $brand . '\) clamp\(0, \(l - 0\.\d+\) \* -\d+, 1\) 0 0\)/'
    );
})->with(['primary', 'secondary']);

it('never hardcodes the text colour over a brand fill', function (): void {
    /** A fixed `text-neutral-950` here is the bug the contrast token exists to prevent. */
    $offenders = collect(File::allFiles(__DIR__ . '/../../resources/views/components'))
        ->flatMap(function (SplFileInfo $file): array {
            preg_match_all('/\bbg-(primary|secondary)\b[^\'"]*/', $file->getContents(), $found);

            return collect($found[0])
                ->filter(fn (string $classes): bool => (bool) preg_match('/\btext-(?!primary-contrast|secondary-contrast)[a-z]+-\d{2,3}\b/', $classes))
                ->map(fn (string $classes): string => $file->getFilename() . ': ' . $classes)
                ->all();
        })
        ->all();

    expect($offenders)->toBeEmpty();
});

it('keeps the ink tied to the brand colour rather than to a fixed one', function (string $brand): void {
    /** Hardcoding a colour here would be the same bug in a new place. */
    expect(theme())->toMatch('/--color-' . $brand . '-ink:\s*oklch\(from var\(--color-' . $brand . '\)/');
})->with(['primary', 'secondary']);

it('forces the ink dark enough to be read on white', function (string $brand): void {
    /**
     * Contrast is almost entirely lightness, which is why OKLCH is the space that lets the
     * brand's hue survive the correction. At 0.45 the six brands measured landed between 6.33
     * and 7.98 against white; 0.50 is the ceiling that keeps a margin over the 4.5:1 minimum.
     */
    preg_match('/--color-' . $brand . '-ink:\s*oklch\(from [^)]*\)\s*([0-9.]+)/', theme(), $matches);

    expect($matches[1] ?? null)->not->toBeNull()
        ->and((float) $matches[1])->toBeLessThanOrEqual(0.50);
})->with(['primary', 'secondary']);

it('writes brand colour as a token in the components, never as a numbered shade', function (): void {
    /**
     * A number at the call site pins the component to one palette step, which is what put
     * `text-primary-700` in a link while `--color-primary` was free to be anything.
     *
     * The faint surfaces are the exception and stay: `bg-primary-50` is a tint, not the brand
     * colour, and it follows the scale like everything else.
     */
    $offenders = collect(File::allFiles(__DIR__ . '/../../resources/views/components'))
        ->flatMap(function (SplFileInfo $file): array {
            preg_match_all('/\b(?:bg|text|border|ring|from|to)-(?:primary|secondary)-(\d{2,3})\b/', $file->getContents(), $found);

            return collect($found[0])
                ->reject(fn (string $class): bool => (bool) preg_match('/-(?:50|100|200)$/', $class))
                ->map(fn (string $class): string => $file->getFilename() . ': ' . $class)
                ->all();
        })
        ->all();

    expect($offenders)->toBeEmpty();
});
