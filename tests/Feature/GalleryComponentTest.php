<?php

declare(strict_types = 1);

use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Vite as ViteFacade;

beforeEach(function (): void {
    ViteFacade::clearResolvedInstance();

    app()->instance(Vite::class, new class () extends Vite
    {
        public function asset($asset, $buildDirectory = null): string
        {
            return '/' . ltrim((string) $asset, '/');
        }
    });
});

it('renders a list, not a stack of divs', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery><x-ui.gallery-item src="a.jpg" /></x-ui.gallery>');

    expect($rendered)->toContain('<ul')
        ->toContain('<li')
        ->toContain('grid');
});

it('names the list only when a label is given', function (): void {
    expect((string) $this->blade('<x-ui.gallery label="Obras" />'))->toContain('aria-label="Obras"')
        ->and((string) $this->blade('<x-ui.gallery />'))->not->toContain('aria-label');
});

it('turns the breakpoint map into grid classes', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery :columns="[\'base\' => 2, \'md\' => 3, \'xl\' => 5]" />');

    expect($rendered)->toContain('grid-cols-2')
        ->toContain('md:grid-cols-3')
        ->toContain('xl:grid-cols-5');
});

it('takes a single column count as the base', function (): void {
    expect((string) $this->blade('<x-ui.gallery :columns="4" />'))->toContain('grid-cols-4');
});

it('falls back to two columns when the map names nothing usable', function (): void {
    /** A typo in the breakpoint key left the grid with no column class at all. */
    expect((string) $this->blade('<x-ui.gallery :columns="[\'medio\' => 3]" />'))->toContain('grid-cols-2');
});

it('takes the gap from the whitelist and ignores anything else', function (): void {
    expect((string) $this->blade('<x-ui.gallery :gap="8" />'))->toContain('gap-8')
        ->and((string) $this->blade('<x-ui.gallery :gap="7" />'))->toContain('gap-4');
});

it('leaves an item alone without a lightbox', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery><x-ui.gallery-item src="a.jpg" alt="A" /></x-ui.gallery>');

    expect($rendered)->not->toContain('data-fslightbox')
        ->and($rendered)->toContain('src="/resources/images/a.jpg"');
});

it('wraps the item in a lightbox anchor when the gallery asks for one', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox="obras"><x-ui.gallery-item src="a.jpg" alt="A" /></x-ui.gallery>');

    expect($rendered)->toContain('data-fslightbox="obras"')
        ->toContain('href="/resources/images/a.jpg"');
});

it('names the group gallery when lightbox comes without a value', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item src="a.jpg" /></x-ui.gallery>');

    expect($rendered)->toContain('data-fslightbox="gallery"');
});

it('opens the large source while showing the thumb', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item src="thumb.jpg" source="/grande.jpg" alt="A" /></x-ui.gallery>');

    expect($rendered)->toContain('href="/grande.jpg"')
        ->toContain('src="/resources/images/thumb.jpg"');
});

it('keeps a remote url as it is, instead of resolving it through vite', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item src="https://exemplo.com/a.jpg" /></x-ui.gallery>');

    expect($rendered)->toContain('href="https://exemplo.com/a.jpg"')
        ->not->toContain('/resources/images/https');
});

it('carries the source type through to the anchor', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item src="a.jpg" source="https://youtu.be/x" type="youtube" /></x-ui.gallery>');

    expect($rendered)->toContain('data-type="youtube"');
});

it('leaves a slot item without src outside the lightbox', function (): void {
    /** Free markup in the grid has nothing to open, and an anchor with no href is what fsLightbox warns about. */
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item><p>Texto</p></x-ui.gallery-item></x-ui.gallery>');

    expect($rendered)->toContain('<p>Texto</p>')
        ->not->toContain('data-fslightbox');
});

it('prefers the slot over the generated image', function (): void {
    $rendered = (string) $this->blade('<x-ui.gallery lightbox><x-ui.gallery-item src="a.jpg"><span>Meu</span></x-ui.gallery-item></x-ui.gallery>');

    expect($rendered)->toContain('<span>Meu</span>')
        ->and($rendered)->not->toContain('<img');
});

it('keeps the caller classes on the list', function (): void {
    expect((string) $this->blade('<x-ui.gallery class="mt-10" />'))->toContain('mt-10');
});

it('lays the pictures out in columns when asked for masonry', function (): void {
    $blade = <<<'BLADE'
        <x-ui.gallery masonry :columns="['base' => 2, 'md' => 3]">
            <x-ui.gallery-item src="https://cdn.example.com/a.jpg" />
        </x-ui.gallery>
        BLADE;

    $html = (string) $this->blade($blade);

    expect($html)->toContain('columns-2')
        ->toContain('md:columns-3')
        ->not->toContain('grid-cols-')
        ->and($html)->not->toMatch('/class="[^"]*\bgrid\b/');
});

it('keeps each masonry picture at its own height, spaced by its own margin', function (): void {
    $blade = <<<'BLADE'
        <x-ui.gallery masonry :gap="6">
            <x-ui.gallery-item src="https://cdn.example.com/a.jpg" />
        </x-ui.gallery>
        BLADE;

    expect((string) $this->blade($blade))
        ->toContain('break-inside-avoid')
        ->toContain('mb-6')
        ->not->toContain('object-cover');
});

it('stays a grid by default, cropping to the row', function (): void {
    $blade = <<<'BLADE'
        <x-ui.gallery>
            <x-ui.gallery-item src="https://cdn.example.com/a.jpg" />
        </x-ui.gallery>
        BLADE;

    expect((string) $this->blade($blade))
        ->toContain('grid-cols-2')
        ->toContain('object-cover')
        ->not->toContain('break-inside-avoid');
});
