<?php

declare(strict_types = 1);

it('is a paragraph by default', function (): void {
    expect((string) $this->blade('<x-ui.text>Corpo</x-ui.text>'))
        ->toStartWith('<p')
        ->toContain('</p>');
});

it('becomes a span when it sits inside a sentence', function (): void {
    expect((string) $this->blade('<x-ui.text inline>trecho</x-ui.text>'))
        ->toStartWith('<span')
        ->toContain('</span>');
});

it('drops the block typography when inline', function (): void {
    /**
     * `leading-relaxed` is line spacing for a paragraph and `text-pretty` rebalances the last
     * lines of a block. An inline run has neither, and carrying them would change the spacing
     * of the sentence it sits in.
     */
    expect((string) $this->blade('<x-ui.text inline>t</x-ui.text>'))
        ->not->toContain('leading-relaxed')
        ->not->toContain('text-pretty')
        ->and((string) $this->blade('<x-ui.text>t</x-ui.text>'))
        ->toContain('leading-relaxed')
        ->toContain('text-pretty');
});

it('carries the four sizes', function (string $size, string $expected): void {
    expect((string) $this->blade('<x-ui.text size="' . $size . '">t</x-ui.text>'))->toContain($expected);
})->with([
    ['sm', 'text-sm'],
    ['base', 'text-base'],
    ['lg', 'text-lg'],
    ['xl', 'text-xl'],
]);

it('keeps the subtle tone readable', function (): void {
    /**
     * `neutral-600` and not a lighter grey: at 16px on white it measures 7.56:1, while
     * `neutral-400` measures 2.6 and fails the 4.5:1 body copy owes.
     */
    expect((string) $this->blade('<x-ui.text variant="subtle">t</x-ui.text>'))
        ->toContain('text-neutral-600')
        ->not->toContain('text-neutral-400');
});

it('has no colour prop, only tone', function (): void {
    /**
     * Flux offers seventeen palette names here. Seventeen names is a list to maintain and a
     * vocabulary to learn; a Tailwind class at the call site is neither, and it puts the
     * semantic colour where its meaning is.
     */
    expect(componentSource('text'))
        ->toContain("'variant' => 'default'")
        ->not->toContain("'color'")
        ->and((string) $this->blade('<x-ui.text class="text-red-700">t</x-ui.text>'))
        ->toContain('text-red-700');
});

it('falls back for a variant that is not one', function (): void {
    expect((string) $this->blade('<x-ui.text variant="berrante">t</x-ui.text>'))
        ->toContain('text-neutral-700')
        ->not->toContain('berrante');
});
