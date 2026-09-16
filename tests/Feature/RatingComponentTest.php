<?php

declare(strict_types = 1);

it('reports a number when it has no name', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating :value="4.5" />');

    expect($rendered)->toContain('role="img"')
        ->toContain('aria-label="4,5 de 5"')
        ->not->toContain('<input');
});

it('becomes a form field once it has a name', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="nota" />');

    expect($rendered)->toContain('<fieldset')
        ->and(substr_count($rendered, 'type="radio"'))->toBe(5)
        ->and($rendered)->toContain('name="nota"');
});

it('clips the overlay to the share of the maximum', function (float $value, int $max, string $width): void {
    $this->blade('<x-ui.rating :value="' . $value . '" :max="' . $max . '" />')->assertSee('width: ' . $width, false);
})->with([
    [4.5, 5, '90%'],
    [4.3, 5, '86%'],
    [0.0, 5, '0%'],
    [5.0, 5, '100%'],
    [3.0, 4, '75%'],
]);

it('never overflows its own scale', function (): void {
    $this->blade('<x-ui.rating :value="9" :max="5" />')
        ->assertSee('width: 100%', false)
        ->assertSee('aria-label="5 de 5"', false);

    $this->blade('<x-ui.rating :value="-2" :max="5" />')->assertSee('width: 0%', false);
});

it('draws one icon per step on both layers', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating :value="2" :max="4" />');

    /** A muted row plus the clipped overlay, so every step is drawn twice. */
    expect(substr_count($rendered, '<svg'))->toBe(8);
});

it('orders the inputs from the maximum down, so a checked star can reach the lower ones', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="nota" :max="3" />');

    $positions = collect([1, 2, 3])->map(fn (int $star): int => strpos($rendered, 'value="' . $star . '"'));

    expect($positions[2])->toBeLessThan($positions[1])
        ->and($positions[1])->toBeLessThan($positions[0])
        ->and($rendered)->toContain('flex-row-reverse');
});

it('checks the star that matches the value', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="nota" :value="3" />');

    expect($rendered)->toMatch('/value="3"\s+class="peer sr-only"\s+checked/')
        ->and(substr_count($rendered, 'checked'))->toBe(1);
});

it('names every star for a screen reader', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="nota" :max="3" />');

    expect($rendered)->toContain('1 de 3')
        ->toContain('2 de 3')
        ->toContain('3 de 3');
});

it('labels the group, with an override', function (): void {
    $this->blade('<x-ui.rating name="nota" :max="4" />')->assertSee('Nota de 1 a 4', false);

    $this->blade('<x-ui.rating name="nota" label="Como foi o atendimento?" />')
        ->assertSee('Como foi o atendimento?', false);

    $this->blade('<x-ui.rating :value="4" label="Quatro de cinco" />')
        ->assertSee('aria-label="Quatro de cinco"', false);
});

it('adds an empty radio only when the rating can be cleared', function (): void {
    $withClear = (string) $this->blade('<x-ui.rating name="nota" clearable />');

    expect($withClear)->toContain('value=""')
        ->and(substr_count($withClear, 'type="radio"'))->toBe(6)
        ->and((string) $this->blade('<x-ui.rating name="nota" />'))->not->toContain('value=""');
});

it('disables every input at once', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="nota" :max="3" disabled />');

    expect(substr_count($rendered, 'disabled'))->toBe(4)
        ->and($rendered)->toContain('opacity-50');
});

it('sizes the icon', function (string $size, string $expected): void {
    $this->blade('<x-ui.rating :value="3" size="' . $size . '" />')->assertSee($expected, false);
})->with([
    ['xs', 'size-4'],
    ['sm', 'size-5'],
    ['base', 'size-6'],
    ['lg', 'size-7'],
    ['xl', 'size-9'],
]);

it('swaps the shape', function (): void {
    expect((string) $this->blade('<x-ui.rating :value="3" shape="heart" />'))->toContain('m11.645 20.91');

    /** An unknown shape falls back to the star rather than rendering nothing. */
    expect((string) $this->blade('<x-ui.rating :value="3" shape="losango" />'))->toContain('<svg');
});

it('gives each group its own ids so two ratings can share a page', function (): void {
    $rendered = (string) $this->blade('<x-ui.rating name="a" /><x-ui.rating name="b" />');

    preg_match_all('/for="([^"]+)"/', $rendered, $matches);

    expect($matches[1])->toHaveCount(10)
        ->and(array_unique($matches[1]))->toHaveCount(10);
});

it('rings the star the keyboard is on', function (): void {
    /**
     * The inputs are `sr-only`, so the library's focus rule paints a ring nobody can see and
     * the label has to wear it. It has to be the label immediately after the focused input:
     * `~ label:first-of-type` matched the first label in the fieldset, which only ever
     * follows the top star's input — every other star was focusable with no ring at all.
     */
    $css = file_get_contents(__DIR__ . '/../../resources/css/ui.css');

    expect($css)
        ->toContain('[data-rating] input:focus-visible + label')
        ->not->toContain('[data-rating] input:focus-visible ~ label');
});
