<?php

declare(strict_types = 1);

use Goognet\Ui\Ui;

/**
 * @return list<string>
 */
function buttonClasses(string $html): array
{
    preg_match('/<(?:button|a)\b[^>]*\bclass="([^"]*)"/', $html, $matches);

    return explode(' ', $matches[1] ?? '');
}

it('uses a site default when the call site passes nothing', function (): void {
    Ui::button()->defaults(['variant' => 'primary', 'size' => 'lg', 'rounded' => 'full']);

    expect(buttonClasses((string) $this->blade('<x-ui.button>Ok</x-ui.button>')))
        ->toContain('bg-primary')
        ->toContain('h-control-lg')
        ->toContain('rounded-full');
});

it('still lets the call site override a site default', function (): void {
    Ui::button()->defaults(['variant' => 'primary']);

    expect(buttonClasses((string) $this->blade('<x-ui.button variant="ghost">Ok</x-ui.button>')))
        ->toContain('bg-transparent')
        ->not->toContain('bg-primary');
});

it('adds a variant of its own', function (): void {
    Ui::button()->variant('outline', 'border-2 border-primary bg-transparent text-primary-ink');

    expect(buttonClasses((string) $this->blade('<x-ui.button variant="outline">Ok</x-ui.button>')))
        ->toContain('border-2')
        ->toContain('border-primary');
});

it('replaces a variant the package ships', function (): void {
    Ui::button()->variant('primary', 'bg-black text-white');

    expect(buttonClasses((string) $this->blade('<x-ui.button variant="primary">Ok</x-ui.button>')))
        ->toContain('bg-black')
        ->not->toContain('bg-primary');
});

it('adds a size of its own', function (): void {
    Ui::button()->size('xl', 'h-14 px-8 text-lg');

    expect(buttonClasses((string) $this->blade('<x-ui.button size="xl">Ok</x-ui.button>')))
        ->toContain('h-14')
        ->toContain('text-lg');
});

it('lays classes over a part, replacing the utilities it overlaps', function (): void {
    Ui::button()->part('base', 'uppercase rounded-none tracking-wide');

    expect(buttonClasses((string) $this->blade('<x-ui.button>Ok</x-ui.button>')))
        ->toContain('uppercase')
        ->toContain('rounded-none')
        ->not->toContain('rounded-control')
        ->toContain('inline-flex');
});

it('replaces a part entirely when asked to', function (): void {
    Ui::button()->replacePart('base', 'meu-botao');

    expect((string) $this->blade('<x-ui.button>Ok</x-ui.button>'))->toContain('class="meu-botao"');
});

it('customises the inner parts as well', function (): void {
    Ui::button()->part('content', 'gap-4')->part('icon', 'size-6');

    $html = (string) $this->blade('<x-ui.button icon="heroicon-m-plus">Ok</x-ui.button>');

    expect($html)->toContain('gap-4')
        ->not->toContain('gap-2')
        ->toContain('size-6');
});

it('gives the call site the last word over the site customisation', function (): void {
    Ui::button()->part('base', 'rounded-none');

    expect(buttonClasses((string) $this->blade('<x-ui.button class="rounded-full">Ok</x-ui.button>')))
        ->toContain('rounded-full')
        ->not->toContain('rounded-none');
});

it('lets a class at the call site replace the package utilities', function (): void {
    expect(buttonClasses((string) $this->blade('<x-ui.button class="rounded-full h-14 font-bold">Ok</x-ui.button>')))
        ->toContain('rounded-full')
        ->toContain('h-14')
        ->toContain('font-bold')
        ->not->toContain('rounded-control')
        ->not->toContain('h-control')
        ->not->toContain('font-control');
});

it('names the component by its method, in camel case', function (): void {
    expect(Ui::carouselSlide())->toBe(Ui::component('carousel-slide'));
});

it('refuses a component the package does not have, typo or path', function (string $name): void {
    Ui::component($name);
})->with(['buton', '../config/goognet-ui', 'Button'])->throws(BadMethodCallException::class);

it('keeps the look unchanged until a site customises it', function (): void {
    $css = (string) file_get_contents(__DIR__ . '/../../resources/css/ui.css');

    expect($css)->toContain('--radius-control: 0.5rem;')
        ->toContain('--font-weight-control: 500;')
        ->toContain('--spacing-control: 2.5rem;');
});
