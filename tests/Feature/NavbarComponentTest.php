<?php

declare(strict_types = 1);

it('renders a header wrapping a container', function (): void {
    $this->blade('<x-ui.navbar>conteúdo</x-ui.navbar>')
        ->assertSee('<header', false)
        ->assertSee('conteúdo')
        ->assertSee('flex h-20 items-center justify-between gap-6', false);
});

it('sticks to the top by default', function (): void {
    $this->blade('<x-ui.navbar>x</x-ui.navbar>')
        ->assertSee('sticky top-0 z-40', false);
});

it('applies each position', function (string $position, string $expected): void {
    $this->blade('<x-ui.navbar position="' . $position . '">x</x-ui.navbar>')
        ->assertSee($expected, false);
})->with([
    ['static', 'relative border-b border-neutral-200 bg-white'],
    ['sticky', 'sticky top-0 z-40'],
    ['overlay', 'fixed inset-x-0 top-0 z-40'],
]);

it('falls back to sticky for an unknown position', function (): void {
    $this->blade('<x-ui.navbar position="nope">x</x-ui.navbar>')
        ->assertSee('sticky top-0 z-40', false);
});

it('gives every position a positioning context for the megamenu panel', function (string $position): void {
    $rendered = (string) $this->blade('<x-ui.navbar position="' . $position . '">x</x-ui.navbar>');

    expect($rendered)->toMatch('/class="[^"]*\b(relative|sticky|fixed)\b/');
})->with(['static', 'sticky', 'overlay']);

it('marks only an overlay navbar for the scroll script', function (): void {
    $this->blade('<x-ui.navbar position="overlay">x</x-ui.navbar>')
        ->assertSee('data-navbar', false)
        ->assertSee('data-scrolled="false"', false);

    $this->blade('<x-ui.navbar>x</x-ui.navbar>')->assertDontSee('data-navbar', false);
});

it('turns an overlay solid once scrolled', function (): void {
    $this->blade('<x-ui.navbar position="overlay">x</x-ui.navbar>')
        ->assertSee('data-[scrolled=false]:bg-transparent', false)
        ->assertSee('data-[scrolled=false]:border-transparent', false)
        ->assertSee('data-[scrolled=true]:shadow-control', false);
});

it('renders the cta slot beside the content', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <span>marca</span>
            <x-slot:cta><span>Orçamento</span></x-slot>
        </x-ui.navbar>
        BLADE;

    $this->blade($template)
        ->assertSeeInOrder(['marca', 'Orçamento'])
        ->assertSee('hidden items-center lg:flex', false);
});

it('renders no cta wrapper when no cta is given', function (): void {
    expect((string) $this->blade('<x-ui.navbar>x</x-ui.navbar>'))
        ->not->toContain('hidden items-center lg:flex');
});

it('merges attributes onto the header', function (): void {
    $this->blade('<x-ui.navbar class="shadow-lg">x</x-ui.navbar>')
        ->assertSee('shadow-lg', false);
});

it('composes with the brand and the menu', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-ui.brand :href="'/'" logo="https://exemplo.com/logo.svg" class="h-8 w-auto" />
            <x-ui.menu :items="[['label' => 'Início', 'url' => '/']]" />
        </x-ui.navbar>
        BLADE;

    $this->blade($template)
        ->assertSee('data-menu', false)
        ->assertSee('data-menu-toggle', false)
        ->assertSee('<img', false);
});

it('puts the menu drawer slot at the bottom of the drawer', function (): void {
    $template = <<<'BLADE'
        <x-ui.menu :items="[['label' => 'Início', 'url' => '/']]">
            <span>Peça um orçamento</span>
        </x-ui.menu>
        BLADE;

    $rendered = (string) $this->blade($template);

    preg_match('/data-menu-panel.*$/s', $rendered, $drawer);

    expect($drawer[0])->toContain('Peça um orçamento')
        ->toContain('mt-auto border-t');
});

it('does not react to scroll unless asked to', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar>x</x-ui.navbar>');

    expect($rendered)->not->toContain('data-navbar')
        ->and($rendered)->not->toContain('data-auto-hide')
        ->and($rendered)->not->toContain('data-[hidden=true]');
});

it('hides on scroll down when asked', function (): void {
    $this->blade('<x-ui.navbar auto-hide>x</x-ui.navbar>')
        ->assertSee('data-auto-hide="true"', false)
        ->assertSee('data-hidden="false"', false)
        ->assertSee('data-[hidden=true]:-top-full', false)
        /** Named properties, not `all`: the bar reacts to every scroll frame, and `all` puts
         *  each changed property through the easing, including ones the browser recalculates
         *  while the element is stuck. */
        ->assertSee('transition-[top,background-color,border-color,box-shadow]', false)
        ->assertDontSee('transition-all', false);
});

it('marks an auto-hiding navbar for the script', function (): void {
    $this->blade('<x-ui.navbar auto-hide>x</x-ui.navbar>')
        ->assertSee('data-navbar', false);
});

it('ignores auto-hide on a static navbar, which scrolls away anyway', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar position="static" auto-hide>x</x-ui.navbar>');

    expect($rendered)->not->toContain('data-auto-hide')
        ->and($rendered)->not->toContain('data-[hidden=true]')
        ->and($rendered)->not->toContain('data-navbar');
});

it('combines auto-hide with the overlay solid swap', function (): void {
    $this->blade('<x-ui.navbar position="overlay" auto-hide>x</x-ui.navbar>')
        ->assertSee('data-[scrolled=false]:bg-transparent', false)
        ->assertSee('data-[hidden=true]:-top-full', false)
        ->assertSee('data-auto-hide="true"', false);
});

it('starts visible', function (): void {
    $this->blade('<x-ui.navbar auto-hide>x</x-ui.navbar>')
        ->assertSee('data-hidden="false"', false)
        ->assertDontSee('data-hidden="true"', false);
});

it('hides by moving top, never by transforming', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar auto-hide>x</x-ui.navbar>');

    /**
     * A transformed ancestor becomes the containing block for its fixed
     * descendants, which drags the menu drawer into the document and widens
     * the page on mobile.
     */
    expect($rendered)->toContain('data-[hidden=true]:-top-full')
        ->and($rendered)->not->toContain('translate-y');
});

it('pins an overlay to the viewport so it follows the scroll', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar position="overlay">x</x-ui.navbar>');

    /** `absolute` would anchor it to the document and scroll it out of view. */
    expect($rendered)->toContain('fixed inset-x-0 top-0')
        ->and($rendered)->not->toContain('absolute');
});

it('defaults to a white bar', function (): void {
    $this->blade('<x-ui.navbar>x</x-ui.navbar>')->assertSee('bg-white', false);
});

it('drops its own background when a bg class is passed', function (string $class): void {
    $rendered = (string) $this->blade('<x-ui.navbar class="' . $class . '">x</x-ui.navbar>');

    preg_match('/<header[^>]*class="([^"]*)"/s', $rendered, $header);

    $classes = preg_split('/\s+/', trim($header[1]));

    expect($classes)->toContain($class)
        ->and($classes)->not->toContain('bg-white');
})->with(['bg-neutral-900', 'bg-primary', 'bg-white/80']);

it('keeps its own background when the class touches something else', function (): void {
    $this->blade('<x-ui.navbar class="shadow-lg">x</x-ui.navbar>')
        ->assertSee('bg-white', false)
        ->assertSee('shadow-lg', false);
});

it('renders exactly one class attribute on the header', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar class="bg-neutral-900">x</x-ui.navbar>');

    preg_match('/<header[^>]*>/s', $rendered, $header);

    expect(substr_count($header[0], 'class='))->toBe(1);
});

it('cancels the overlay background while unscrolled instead of adding one when scrolled', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar position="overlay" class="bg-neutral-900">x</x-ui.navbar>');

    /** The attribute selector outranks a plain utility, so the passed colour survives. */
    expect($rendered)->toContain('data-[scrolled=false]:bg-transparent')
        ->toContain('data-[scrolled=false]:border-transparent')
        ->toContain('bg-neutral-900')
        ->and($rendered)->not->toContain('data-[scrolled=true]:bg-');
});

it('draws a bottom border by default', function (string $position): void {
    $this->blade('<x-ui.navbar position="' . $position . '">x</x-ui.navbar>')
        ->assertSee('border-b border-neutral-200', false);
})->with(['static', 'sticky', 'overlay']);

it('drops the border entirely when asked', function (string $position): void {
    $rendered = (string) $this->blade('<x-ui.navbar position="' . $position . '" :border="false">x</x-ui.navbar>');

    expect($rendered)->not->toContain('border-b')
        ->and($rendered)->not->toContain('border-neutral-200')
        ->and($rendered)->not->toContain('border-transparent');
})->with(['static', 'sticky', 'overlay']);

it('keeps the overlay background swap when the border is off', function (): void {
    $this->blade('<x-ui.navbar position="overlay" :border="false">x</x-ui.navbar>')
        ->assertSee('data-[scrolled=false]:bg-transparent', false)
        ->assertSee('data-[scrolled=true]:shadow-control', false);
});

it('lets a passed border colour replace the default', function (): void {
    $rendered = (string) $this->blade('<x-ui.navbar class="border-neutral-800">x</x-ui.navbar>');

    preg_match('/<header[^>]*class="([^"]*)"/s', $rendered, $header);

    $classes = preg_split('/\s+/', trim($header[1]));

    expect($classes)->toContain('border-b')
        ->toContain('border-neutral-800')
        ->and($classes)->not->toContain('border-neutral-200');
});

it('still borders when the class touches something else', function (): void {
    $this->blade('<x-ui.navbar class="shadow-lg">x</x-ui.navbar>')
        ->assertSee('border-b border-neutral-200', false);
});

it('renders no info strip unless one is given', function (): void {
    expect((string) $this->blade('<x-ui.navbar>x</x-ui.navbar>'))
        ->not->toContain('h-12');
});

it('puts the info strip above the bar', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info><span>(11) 4002-8922</span></x-slot>
            <span>marca</span>
        </x-ui.navbar>
        BLADE;

    $this->blade($template)
        ->assertSeeInOrder(['(11) 4002-8922', 'marca'])
        ->assertSee('flex h-12 items-center justify-between gap-4', false);
});

it('separates the info strip from the bar', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info>info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    $this->blade($template)->assertSee('hidden text-sm md:block border-b border-neutral-200/60', false);
});

it('hides the whole header, strip included, when auto-hiding', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar auto-hide>
            <x-slot:info>info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    /** -top-20 would only clear the bar and leave the strip peeking out. */
    $this->blade($template)->assertSee('data-[hidden=true]:-top-full', false);
});

it('keeps the info strip inside the container gutters', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info>info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    $rendered = (string) $this->blade($template);

    /** The width is a token now: `--container-page`. */
    expect(substr_count($rendered, 'max-w-page'))->toBe(2);
});

it('keeps the info strip off small screens', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info>info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    $this->blade($template)->assertSee('hidden text-sm md:block', false);
});

it('lets the info strip carry its own colours', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info class="bg-primary text-neutral-950">info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    $this->blade($template)->assertSee('bg-primary text-neutral-950', false);
});

it('lets a passed border colour replace the strip default', function (): void {
    $template = <<<'BLADE'
        <x-ui.navbar>
            <x-slot:info class="border-primary-700">info</x-slot>
            marca
        </x-ui.navbar>
        BLADE;

    $rendered = (string) $this->blade($template);

    expect($rendered)->toContain('border-primary-700')
        ->and($rendered)->not->toContain('border-neutral-200/60');
});
