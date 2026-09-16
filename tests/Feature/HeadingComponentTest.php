<?php

declare(strict_types = 1);

it('renders a div when no level is given', function (): void {
    /**
     * The outline a screen reader walks is built from headings. A card title that is not a
     * subdivision of the document should not appear in it, so size alone never promotes an
     * element to a heading.
     */
    expect((string) $this->blade('<x-ui.heading>Título de card</x-ui.heading>'))
        ->toStartWith('<div')
        ->not->toContain('<h');
});

it('renders the heading level it is given', function (int $level): void {
    expect((string) $this->blade('<x-ui.heading :level="' . $level . '">T</x-ui.heading>'))
        ->toStartWith('<h' . $level)
        ->toContain('</h' . $level . '>');
})->with([1, 2, 3, 4]);

it('ignores a level that is not a heading', function (mixed $level): void {
    /** A bare value would emit `<h0>` or `<h9>`, which is not an element. */
    $rendered = (string) $this->blade('<x-ui.heading :level="$level">T</x-ui.heading>', ['level' => $level]);

    expect($rendered)->toStartWith('<div');
})->with([0, 7, 'dois']);

it('keeps size and level independent', function (): void {
    /**
     * A section heading can be small and a label can be large. Tying the two would force a page
     * to choose between the outline it needs and the proportions it wants.
     */
    $pequeno = (string) $this->blade('<x-ui.heading :level="2" size="base">T</x-ui.heading>');

    $grande = (string) $this->blade('<x-ui.heading size="2xl">T</x-ui.heading>');

    expect($pequeno)->toStartWith('<h2')->toContain('text-base')
        ->and($grande)->toStartWith('<div')->toContain('text-4xl');
});

it('falls back to the base size for a name that is not one', function (): void {
    expect((string) $this->blade('<x-ui.heading size="gigante">T</x-ui.heading>'))
        ->toContain('text-base')
        ->not->toContain('gigante');
});

it('takes colour from the call site, having no colour prop', function (): void {
    /** Same reasoning as `x-ui.badge`: a semantic colour is written where its meaning is. */
    expect((string) $this->blade('<x-ui.heading class="text-primary-ink">T</x-ui.heading>'))
        ->toContain('text-primary-ink');
});
