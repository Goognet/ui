<?php

declare(strict_types = 1);

it('renders a button by default', function (): void {
    $this->blade('<x-ui.button>Salvar</x-ui.button>')
        ->assertSee('<button', false)
        ->assertSee('type="button"', false)
        ->assertSee('bg-white', false)
        ->assertSee('Salvar');
});

it('renders an anchor when href is given', function (): void {
    $this->blade('<x-ui.button href="/contato" variant="primary">Contato</x-ui.button>')
        ->assertSee('<a', false)
        ->assertSee('href="/contato"', false)
        ->assertSee('bg-primary', false)
        ->assertDontSee('<button', false);
});

it('applies variant classes', function (string $variant, string $expected): void {
    $this->blade('<x-ui.button variant="' . $variant . '">Ok</x-ui.button>')
        ->assertSee($expected, false);
})->with([
    ['default', 'bg-white'],
    ['primary', 'bg-primary'],
    ['secondary', 'bg-secondary'],
    ['filled', 'bg-neutral-100'],
    ['ghost', 'bg-transparent'],
]);

it('applies size and square classes', function (): void {
    $this->blade('<x-ui.button size="sm">Ok</x-ui.button>')->assertSee('h-control-sm px-3', false);
    $this->blade('<x-ui.button size="sm" square>Ok</x-ui.button>')->assertSee('size-control-sm', false);
});

it('merges extra attributes and classes', function (): void {
    $this->blade('<x-ui.button class="w-full" wire:click="save">Ok</x-ui.button>')
        ->assertSee('w-full', false)
        ->assertSee('wire:click="save"', false);
});

it('disables the button when disabled or loading', function (): void {
    $this->blade('<x-ui.button disabled>Ok</x-ui.button>')
        ->assertSee('disabled="disabled"', false)
        ->assertSee('aria-disabled="true"', false);

    $this->blade('<x-ui.button loading>Ok</x-ui.button>')
        ->assertSee('aria-busy="true"', false)
        ->assertSee('animate-spin', false);
});

it('drops the href and blocks clicks on a disabled link', function (): void {
    $this->blade('<x-ui.button href="/x" disabled>Ok</x-ui.button>')
        ->assertDontSee('href=', false)
        ->assertSee('pointer-events-none', false)
        ->assertSee('tabindex="-1"', false);
});

it('renders icon slots', function (): void {
    $template = <<<'BLADE'
        <x-ui.button>
            <x-slot:icon>L</x-slot>
            Ok
            <x-slot:icon-trailing>R</x-slot>
        </x-ui.button>
        BLADE;

    $this->blade($template)->assertSeeInOrder(['L', 'Ok', 'R']);
});

it('renders blade-icons names passed to the icon props', function (): void {
    $this->blade('<x-ui.button icon="heroicon-m-plus" icon-trailing="heroicon-m-plus">Ok</x-ui.button>')
        ->assertSee('<svg', false)
        ->assertSee('class="size-4"', false)
        ->assertSee('data-slot="icon"', false);

    $rendered = (string) $this->blade('<x-ui.button icon="heroicon-m-plus" icon-trailing="heroicon-m-plus">Ok</x-ui.button>');

    expect(substr_count($rendered, '<svg'))->toBe(2);
});

it('scales icons with the button size', function (): void {
    $this->blade('<x-ui.button size="xs" icon="heroicon-m-plus" />')->assertSee('class="size-3.5"', false);
    $this->blade('<x-ui.button size="lg" icon="heroicon-m-plus" />')->assertSee('class="size-5"', false);
});

it('opens the link in a new tab with the external prop', function (): void {
    $this->blade('<x-ui.button href="https://goognet.com.br" external>Site</x-ui.button>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('adds the rel guard when target blank is passed as an attribute', function (): void {
    $this->blade('<x-ui.button href="https://goognet.com.br" target="_blank">Site</x-ui.button>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('keeps a custom rel and never targets a plain button', function (): void {
    $this->blade('<x-ui.button href="/x" external rel="nofollow">Ok</x-ui.button>')
        ->assertSee('rel="nofollow"', false)
        ->assertDontSee('noopener', false);

    $this->blade('<x-ui.button external>Ok</x-ui.button>')
        ->assertDontSee('target=', false)
        ->assertDontSee('rel=', false);
});

it('is rounded by default', function (): void {
    /** The radius is a token, `--radius-control`, so a site reshapes every control from its @theme. */
    $this->blade('<x-ui.button>Ok</x-ui.button>')->assertSee('rounded-control', false);
});

it('renders a pill when rounded is passed as a boolean', function (): void {
    $this->blade('<x-ui.button rounded>Ok</x-ui.button>')->assertSee('rounded-full', false);
});

it('maps the rounded prop to a radius scale', function (string $rounded, string $expected): void {
    $this->blade('<x-ui.button rounded="' . $rounded . '">Ok</x-ui.button>')->assertSee($expected, false);
})->with([
    ['sm', 'rounded-md'],
    ['md', 'rounded-lg'],
    ['base', 'rounded-control'],
    ['none', 'rounded-none'],
    ['lg', 'rounded-xl'],
    ['xl', 'rounded-2xl'],
    ['full', 'rounded-full'],
]);

it('passes an unknown rounded value through as a class', function (): void {
    $this->blade('<x-ui.button rounded="rounded-t-lg">Ok</x-ui.button>')->assertSee('rounded-t-lg', false);
});

it('lifts on hover and settles on press', function (): void {
    /** 1px of movement reads as weight. The old `active:scale-105` grew the control under
     *  the finger, which is the opposite of what pressing something looks like. */
    $rendered = (string) $this->blade('<x-ui.button>Ok</x-ui.button>');

    expect($rendered)->toContain('hover:-translate-y-px')
        ->toContain('active:translate-y-0')
        ->not->toContain('active:scale-105');
});

it('darkens its own surface on hover instead of casting a coloured glow', function (): void {
    /** A tinted shadow behind a button reads as a halo on a white page. */
    $rendered = (string) $this->blade('<x-ui.button variant="primary">Ok</x-ui.button>');

    expect($rendered)->toContain('hover:bg-primary-dark')
        ->not->toContain('hover:shadow-primary');
});

it('moves on the library curve, not the browser default', function (): void {
    expect((string) $this->blade('<x-ui.button>Ok</x-ui.button>'))
        ->toContain('ease-(--ease-fluid)')
        ->toContain('duration-(--duration-base)');
});

it('falls back to the default radius instead of emitting a class that styles nothing', function (): void {
    $rendered = (string) $this->blade('<x-ui.button rounded="redondo">Ok</x-ui.button>');

    expect($rendered)->toContain('rounded-control')
        ->not->toContain(' redondo');
});
