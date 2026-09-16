<?php

declare(strict_types = 1);

it('is a span, and an anchor once it has an href', function (): void {
    expect((string) $this->blade('<x-ui.badge>Novo</x-ui.badge>'))
        ->toContain('<span')
        ->not->toContain('<a ');

    $this->blade('<x-ui.badge href="/tags/novo">Novo</x-ui.badge>')
        ->assertSee('<a', false)
        ->assertSee('href="/tags/novo"', false);
});

it('guards an external badge link', function (): void {
    $this->blade('<x-ui.badge href="https://goognet.com.br" external>Parceiro</x-ui.badge>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('never targets a badge without an href', function (): void {
    expect((string) $this->blade('<x-ui.badge external>Novo</x-ui.badge>'))->not->toContain('target=');
});

it('paints the variants', function (string $variant, string $expected): void {
    $this->blade('<x-ui.badge variant="' . $variant . '">Novo</x-ui.badge>')->assertSee($expected, false);
})->with([
    ['default', 'bg-white'],
    ['primary', 'bg-primary'],
    ['secondary', 'bg-secondary'],
    ['filled', 'bg-neutral-100'],
    ['ghost', 'bg-transparent'],
]);

it('falls back to the default variant for an unknown name', function (): void {
    $this->blade('<x-ui.badge variant="cintilante">Novo</x-ui.badge>')->assertSee('bg-white', false);
});

it('drops the variant colour that the caller replaced', function (): void {
    /** Two background utilities on one element are settled by stylesheet order, not by
     *  the order they were written, so the default has to go rather than be outranked. */
    $rendered = (string) $this->blade('<x-ui.badge class="bg-red-100 text-red-800">Atrasado</x-ui.badge>');

    expect($rendered)->toContain('bg-red-100')
        ->toContain('text-red-800')
        ->not->toContain('bg-white')
        ->not->toContain('text-neutral-800');
});

it('keeps the variant colour the caller did not touch', function (): void {
    $rendered = (string) $this->blade('<x-ui.badge class="bg-red-100">Atrasado</x-ui.badge>');

    expect($rendered)->toContain('bg-red-100')
        ->toContain('text-neutral-800')
        ->not->toContain('bg-white');
});

it('drops the default border when the caller passes one', function (): void {
    $rendered = (string) $this->blade('<x-ui.badge class="border-2 border-red-300">Atrasado</x-ui.badge>');

    expect($rendered)->toContain('border-red-300')
        ->not->toContain('border-neutral-200');
});

it('sizes the box and the text together', function (string $size, string $expected): void {
    $this->blade('<x-ui.badge size="' . $size . '">Novo</x-ui.badge>')->assertSee($expected, false);
})->with([
    ['xs', 'h-5'],
    ['sm', 'h-6'],
    ['base', 'h-7'],
    ['lg', 'h-8'],
]);

it('is a pill unless told otherwise', function (): void {
    $this->blade('<x-ui.badge>Novo</x-ui.badge>')->assertSee('rounded-full', false);

    $this->blade('<x-ui.badge rounded="md">Novo</x-ui.badge>')
        ->assertSee('rounded-md', false)
        ->assertDontSee('rounded-full', false);

    /** An explicit utility passes through untouched. */
    $this->blade('<x-ui.badge rounded="rounded-none">Novo</x-ui.badge>')->assertSee('rounded-none', false);
});

it('falls back to the pill instead of emitting a class that styles nothing', function (): void {
    $rendered = (string) $this->blade('<x-ui.badge rounded="redondo">Novo</x-ui.badge>');

    expect($rendered)->toContain('rounded-full')
        ->not->toContain('"redondo"')
        ->not->toContain(' redondo');
});

it('adds a status dot that follows the text colour', function (): void {
    $rendered = (string) $this->blade('<x-ui.badge dot>Ativo</x-ui.badge>');

    expect($rendered)->toContain('bg-current')
        ->toContain('aria-hidden="true"')
        ->and((string) $this->blade('<x-ui.badge>Ativo</x-ui.badge>'))->not->toContain('bg-current');
});

it('takes an icon by name on either side', function (): void {
    $rendered = (string) $this->blade('<x-ui.badge icon="heroicon-m-check" icon-trailing="heroicon-m-x-mark">Ok</x-ui.badge>');

    expect(substr_count($rendered, '<svg'))->toBe(2);
});

it('keeps the caller classes on the badge', function (): void {
    $this->blade('<x-ui.badge class="uppercase tracking-wide">Novo</x-ui.badge>')
        ->assertSee('uppercase', false)
        ->assertSee('tracking-wide', false);
});
