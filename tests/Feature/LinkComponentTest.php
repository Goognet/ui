<?php

declare(strict_types = 1);

it('renders an anchor with the given href', function (): void {
    $this->blade('<x-ui.link href="/sobre">Sobre nós</x-ui.link>')
        ->assertSee('<a', false)
        ->assertSee('href="/sobre"', false)
        ->assertSee('Sobre nós', false);
});

it('applies variant classes', function (string $variant, string $expected): void {
    $this->blade('<x-ui.link href="/x" variant="' . $variant . '">Ok</x-ui.link>')
        ->assertSee($expected, false);
})->with([
    ['primary', 'hover:text-primary'],
    ['secondary', 'hover:text-secondary'],
    ['neutral', 'hover:text-neutral-900'],
    ['white', 'hover:text-white'],
]);

it('falls back to the neutral hover for an unknown variant', function (): void {
    $this->blade('<x-ui.link href="/x" variant="nope">Ok</x-ui.link>')
        ->assertSee('hover:text-neutral-900', false)
        ->assertDontSee('hover:text-primary', false);
});

it('hovers in neutral by default, since text is neutral unless the page says otherwise', function (): void {
    $this->blade('<x-ui.link href="/x">Ok</x-ui.link>')
        ->assertSee('hover:text-neutral-900', false)
        ->assertDontSee('hover:text-primary', false);
});

it('still hovers in the brand ink when asked for it', function (): void {
    $this->blade('<x-ui.link href="/x" variant="primary">Ok</x-ui.link>')
        ->assertSee('hover:text-primary-ink', false);
});

it('never paints the resting colour, so it inherits the container', function (string $variant): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x" variant="' . $variant . '">Ok</x-ui.link>');

    preg_match('/class="([^"]*)"/', $rendered, $matches);

    $resting = array_filter(
        explode(' ', $matches[1]),
        fn (string $class): bool => str_starts_with($class, 'text-') && ! str_starts_with($class, 'text-current'),
    );

    expect($resting)->toBeEmpty()
        ->and($rendered)->toContain('text-current');
})->with(['primary', 'secondary', 'neutral', 'white', 'none']);

it('emits no hover colour at all for the none variant', function (): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x" variant="none">Ok</x-ui.link>');

    expect($rendered)->not->toContain('hover:text-');
});

it('fades the underline in rather than snapping it on', function (): void {
    /** Only colour and decoration-colour transition: `transition-colors` also animated
     *  border and outline colours the link never changes. */
    $this->blade('<x-ui.link href="/x">Ok</x-ui.link>')
        ->assertSee('underline decoration-transparent hover:decoration-current', false)
        ->assertSee('transition-[color,text-decoration-color]', false)
        ->assertSee('duration-(--duration-fast)', false)
        ->assertSee('ease-(--ease-fluid)', false);
});

it('leaves the focus ring to the library, not to every component', function (): void {
    /** One `:focus-visible` rule in app.css: browsers disagree on the default ring, and
     *  repeating three utilities per component is how they drift apart. */
    expect((string) $this->blade('<x-ui.link href="/x">Ok</x-ui.link>'))
        ->not->toContain('focus-visible:outline-2');
});

it('applies the underline modes', function (string $underline, string $expected): void {
    $this->blade('<x-ui.link href="/x" underline="' . $underline . '">Ok</x-ui.link>')
        ->assertSee($expected, false);
})->with([
    ['hover', 'underline decoration-transparent hover:decoration-current'],
    ['always', 'underline'],
    ['none', 'no-underline'],
]);

it('merges extra attributes and classes', function (): void {
    $this->blade('<x-ui.link href="/x" class="font-semibold" wire:navigate>Ok</x-ui.link>')
        ->assertSee('font-semibold', false)
        ->assertSee('wire:navigate', false);
});

it('opens the link in a new tab with the external prop', function (): void {
    $this->blade('<x-ui.link href="https://goognet.com.br" external>Site</x-ui.link>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('adds the rel guard when target blank is passed as an attribute', function (): void {
    $this->blade('<x-ui.link href="https://goognet.com.br" target="_blank">Site</x-ui.link>')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('keeps a custom rel and never targets an internal link', function (): void {
    $this->blade('<x-ui.link href="/x" external rel="nofollow">Ok</x-ui.link>')
        ->assertSee('rel="nofollow"', false)
        ->assertDontSee('noopener', false);

    $this->blade('<x-ui.link href="/x">Ok</x-ui.link>')
        ->assertDontSee('target=', false)
        ->assertDontSee('rel=', false);
});

it('renders icon slots around the label', function (): void {
    $template = <<<'BLADE'
        <x-ui.link href="/x">
            <x-slot:icon>L</x-slot>
            Ok
            <x-slot:icon-trailing>R</x-slot>
        </x-ui.link>
        BLADE;

    $this->blade($template)->assertSeeInOrder(['L', 'Ok', 'R']);
});

it('renders blade-icons names passed to the icon props', function (): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus" icon-trailing="heroicon-m-plus">Ok</x-ui.link>');

    expect($rendered)->toContain('<svg')
        ->toContain('class="size-full"')
        ->and(substr_count($rendered, '<svg'))->toBe(2);
});

it('scales icons with the surrounding text and spaces them on the right side', function (): void {
    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus">Ok</x-ui.link>')
        ->assertSee('size-[1em] align-[-0.125em] me-1', false);

    $this->blade('<x-ui.link href="/x" icon-trailing="heroicon-m-plus">Ok</x-ui.link>')
        ->assertSee('size-[1em] align-[-0.125em] ms-1', false);
});

it('omits the href attribute when none is given', function (): void {
    $this->blade('<x-ui.link>Ok</x-ui.link>')
        ->assertSee('<a', false)
        ->assertDontSee('href=', false);
});

it('inherits the surrounding font size when no size is given', function (): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x">Ok</x-ui.link>');

    expect($rendered)->not->toMatch('/\btext-(xs|sm|base|lg)\b/');
});

it('applies the size scale', function (string $size, string $expected): void {
    $this->blade('<x-ui.link href="/x" size="' . $size . '">Ok</x-ui.link>')
        ->assertSee($expected, false);
})->with([
    ['xs', 'text-xs'],
    ['sm', 'text-sm'],
    ['base', 'text-base'],
    ['lg', 'text-lg'],
]);

it('ignores an unknown size rather than guessing one', function (): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x" size="nope">Ok</x-ui.link>');

    expect($rendered)->not->toMatch('/\btext-(xs|sm|base|lg)\b/');
});

it('keeps the hover underline out of the layout flow so nothing shifts', function (): void {
    $rendered = (string) $this->blade('<x-ui.link href="/x">Ok</x-ui.link>');

    expect($rendered)->toContain('underline')
        ->and($rendered)->not->toContain('no-underline');
});

it('drops the icon gap when there is no text beside it', function (): void {
    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus" label="Adicionar" />')
        ->assertDontSee('me-1', false)
        ->assertDontSee('ms-1', false);

    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus">Novo</x-ui.link>')
        ->assertSee('me-1', false);
});

it('drops the underline on an icon-only link', function (): void {
    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus" label="Adicionar" />')
        ->assertSee('no-underline', false)
        ->assertDontSee('decoration-transparent', false);
});

it('keeps the underline when a trailing icon sits next to text', function (): void {
    $this->blade('<x-ui.link href="/x" icon-trailing="heroicon-m-plus">Ver mais</x-ui.link>')
        ->assertSee('decoration-transparent', false)
        ->assertSee('ms-1', false);
});

it('treats a trailing-only icon as icon-only too', function (): void {
    $this->blade('<x-ui.link href="/x" icon-trailing="heroicon-m-plus" label="Avançar" />')
        ->assertSee('no-underline', false)
        ->assertDontSee('ms-1', false);
});

it('names an icon-only link for screen readers', function (): void {
    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus" label="Adicionar" />')
        ->assertSee('<span class="sr-only">Adicionar</span>', false);
});

it('renders no sr-only span when no label is given', function (): void {
    $this->blade('<x-ui.link href="/x">Ok</x-ui.link>')
        ->assertDontSee('sr-only', false);
});

it('stays a normal link when the slot has text, even with a label', function (): void {
    $this->blade('<x-ui.link href="/x" icon="heroicon-m-plus" label="Extra">Novo</x-ui.link>')
        ->assertSee('decoration-transparent', false)
        ->assertSee('me-1', false)
        ->assertSee('<span class="sr-only">Extra</span>', false);
});

it('renders without a deprecation notice when no size is given', function (): void {
    $notices = [];

    set_error_handler(function (int $level, string $message) use (&$notices): bool {
        $notices[] = $message;

        return true;
    }, E_DEPRECATED | E_WARNING);

    try {
        $this->blade('<x-ui.link href="/x">Ok</x-ui.link>');
    } finally {
        restore_error_handler();
    }

    expect($notices)->toBeEmpty();
});

it('hovers to a tone that is readable as text', function (string $variant, string $expected): void {
    /**
     * Measured on white: the brand lime is 1.95:1 as text and the brand purple 4.12:1, both
     * under the 4.5:1 AA asks. Hovering used to make a link harder to read than leaving it
     * alone. Those colours stay on the filled variants, where ink sits on top of them instead.
     *
     * `ink` rather than a numbered step: it is derived from whatever colour the site sets, so
     * a repaint carries the hover with it.
     */
    $rendered = (string) $this->blade('<x-ui.link href="/x" variant="' . $variant . '">Ir</x-ui.link>');

    expect($rendered)
        ->toContain($expected)
        ->not->toContain('hover:text-primary ')
        ->not->toContain('hover:text-secondary ');
})->with([
    ['primary', 'hover:text-primary-ink'],
    ['secondary', 'hover:text-secondary-ink'],
]);
