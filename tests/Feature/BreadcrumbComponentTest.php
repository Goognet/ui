<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\URL;

/**
 * @return array<int, array{label: string, url?: string}>
 */
function trail(): array
{
    return [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Serviços', 'url' => '/servicos'],
        ['label' => 'Consultoria'],
    ];
}

/**
 * @return array<string, mixed>
 */
function renderedSchema(string $html): array
{
    expect($html)->toMatch('/<script type="application\/ld\+json">/');

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    return json_decode(trim($matches[1]), true, flags: JSON_THROW_ON_ERROR);
}

it('renders a labelled nav wrapping an ordered list', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()])
        ->assertSee('<nav', false)
        ->assertSee('aria-label="Breadcrumb"', false)
        ->assertSee('<ol', false);
});

it('renders nothing when the trail is empty', function (): void {
    $rendered = trim((string) $this->blade('<x-ui.breadcrumb :items="[]" />'));

    expect($rendered)->toBeEmpty();
});

it('links every item except the current page', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect(substr_count($rendered, '<a '))->toBe(2)
        ->and($rendered)->toContain('href="/"')
        ->toContain('href="/servicos"');
});

it('marks the last item as the current page instead of linking it', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect($rendered)->toContain('aria-current="page"')
        ->and(substr_count($rendered, 'aria-current="page"'))->toBe(1)
        ->and($rendered)->not->toContain('href="/consultoria"');
});

it('never links the last item even when it carries a url', function (): void {
    $items = [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Atual', 'url' => '/atual'],
    ];

    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]);

    expect($rendered)->not->toContain('href="/atual"')
        ->and($rendered)->toContain('aria-current="page"');
});

it('renders a separator between items but not before the first', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect(substr_count($rendered, 'aria-hidden="true"'))->toBeGreaterThanOrEqual(2)
        ->and(substr_count($rendered, '<li'))->toBe(3);
});

it('accepts a blade-icons name as the separator', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" separator="heroicon-m-slash" />', ['items' => trail()])
        ->assertSee('<svg', false);
});

it('accepts a separator slot for plain text', function (): void {
    $template = <<<'BLADE'
        <x-ui.breadcrumb :items="$items">
            <x-slot:separator>/</x-slot>
        </x-ui.breadcrumb>
        BLADE;

    $rendered = (string) $this->blade($template, ['items' => trail()]);

    expect($rendered)->not->toContain('<svg')
        ->and(preg_match_all('/aria-hidden="true">\s*\/\s*<\/span>/', $rendered))->toBe(2);
});

it('emits a BreadcrumbList with one positioned entry per item', function (): void {
    $schema = renderedSchema((string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]));

    expect($schema['@context'])->toBe('https://schema.org')
        ->and($schema['@type'])->toBe('BreadcrumbList')
        ->and($schema['itemListElement'])->toHaveCount(3)
        ->and(array_column($schema['itemListElement'], 'position'))->toBe([1, 2, 3])
        ->and(array_column($schema['itemListElement'], 'name'))->toBe(['Início', 'Serviços', 'Consultoria']);
});

it('makes schema urls absolute and omits item for the current page', function (): void {
    URL::forceRootUrl('https://goognet.com.br');

    /**
     * The scheme too, not only the host: `forceRootUrl` leaves the scheme to the generator,
     * which takes it from `APP_URL`. Without this the test asserted https while quietly
     * depending on whatever scheme the local `.env` happened to carry — and it failed the
     * moment it ran against `.env.example`, which is http.
     */
    URL::forceScheme('https');

    $schema = renderedSchema((string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]));

    expect($schema['itemListElement'][0]['item'])->toBe('https://goognet.com.br')
        ->and($schema['itemListElement'][1]['item'])->toBe('https://goognet.com.br/servicos')
        ->and($schema['itemListElement'][2])->not->toHaveKey('item');
});

it('leaves an already absolute url untouched in the schema', function (): void {
    $items = [['label' => 'Parceiro', 'url' => 'https://parceiro.com/pagina'], ['label' => 'Atual']];

    $schema = renderedSchema((string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]));

    expect($schema['itemListElement'][0]['item'])->toBe('https://parceiro.com/pagina');
});

it('escapes a label that tries to break out of the json-ld script', function (): void {
    $items = [['label' => '</script><script>alert(1)</script>', 'url' => '/'], ['label' => 'Atual']];

    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]);

    expect($rendered)->not->toContain('</script><script>alert(1)')
        ->and(renderedSchema($rendered)['itemListElement'][0]['name'])->toBe('</script><script>alert(1)</script>');
});

it('merges attributes onto the nav', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" class="mb-6" />', ['items' => trail()])
        ->assertSee('class="mb-6"', false);
});

it('accepts a collection as well as an array', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => collect(trail())]);

    expect(substr_count($rendered, '<li'))->toBe(3);
});

it('links with a coloured hover by default, without being told to', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()])
        ->assertSee('hover:text-primary', false);
});

it('passes the variant through to every link', function (string $variant, string $expected): void {
    $rendered = (string) $this->blade(
        '<x-ui.breadcrumb :items="$items" variant="' . $variant . '" />',
        ['items' => trail()],
    );

    expect(substr_count($rendered, $expected))->toBe(2);
})->with([
    ['primary', 'hover:text-primary'],
    ['secondary', 'hover:text-secondary'],
    ['neutral', 'hover:text-neutral-900'],
    ['white', 'hover:text-white'],
]);

it('leads with the current page through weight, not colour', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect($rendered)->toMatch('/class="font-medium"\s+aria-current="page"/');
});

it('recedes the separator and the links through opacity', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect($rendered)->toContain('<span class="opacity-50" aria-hidden="true">')
        ->and(substr_count($rendered, 'opacity-70 hover:opacity-100'))->toBe(2);
});

it('hard-codes no colour, so the trail reads on any background', function (string $variant): void {
    $rendered = (string) $this->blade(
        '<x-ui.breadcrumb :items="$items" variant="' . $variant . '" />',
        ['items' => trail()],
    );

    preg_match_all('/(?<!hover:)\b(text-(?:neutral|white|primary|secondary)[^\s"]*)/', $rendered, $matches);

    $resting = array_filter($matches[1], fn (string $class): bool => ! str_starts_with($class, 'text-current'));

    expect(array_values($resting))->toBeEmpty();
})->with(['primary', 'secondary', 'neutral']);

it('sizes the text and the separator icon together', function (string $size, string $text, string $icon): void {
    $rendered = (string) $this->blade(
        '<x-ui.breadcrumb :items="$items" size="' . $size . '" />',
        ['items' => trail()],
    );

    expect($rendered)->toContain('gap-1.5 ' . $text . '"')
        ->and($rendered)->toContain('<svg class="' . $icon . '"');
})->with([
    ['xs', 'text-xs', 'size-3.5'],
    ['sm', 'text-sm', 'size-4'],
    ['base', 'text-base', 'size-4'],
    ['lg', 'text-lg', 'size-5'],
]);

it('defaults to the small size', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()])
        ->assertSee('gap-1.5 text-sm"', false);
});

it('falls back to the small size for an unknown one', function (): void {
    $this->blade('<x-ui.breadcrumb :items="$items" size="nope" />', ['items' => trail()])
        ->assertSee('gap-1.5 text-sm"', false);
});

it('renders an icon on a linked item', function (): void {
    $items = [
        ['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'],
        ['label' => 'Atual'],
    ];

    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]);

    expect(substr_count($rendered, '<svg'))->toBe(2)
        ->and($rendered)->toContain('size-[1em] align-[-0.125em] me-1');
});

it('renders an icon on the current page too', function (): void {
    $items = [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Consultoria', 'icon' => 'heroicon-m-briefcase'],
    ];

    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]);

    expect($rendered)->toContain('class="me-1 inline-block size-[1em] align-[-0.125em]"')
        ->and($rendered)->toMatch('/class="font-medium"\s+aria-current="page"/');
});

it('sizes an item icon with the text, not with the separator', function (string $size): void {
    $items = [['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'], ['label' => 'Atual']];

    $rendered = (string) $this->blade(
        '<x-ui.breadcrumb :items="$items" size="' . $size . '" />',
        ['items' => $items],
    );

    expect($rendered)->toContain('size-[1em]');
})->with(['xs', 'sm', 'base', 'lg']);

it('leaves items without an icon untouched', function (): void {
    $rendered = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => trail()]);

    expect(substr_count($rendered, '<svg'))->toBe(2)
        ->and($rendered)->not->toContain('size-[1em]');
});

it('keeps icons out of the structured data', function (): void {
    $items = [
        ['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'],
        ['label' => 'Atual', 'icon' => 'heroicon-m-briefcase'],
    ];

    $schema = renderedSchema((string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => $items]));

    expect($schema['itemListElement'][0])->not->toHaveKey('icon')
        ->and($schema['itemListElement'][0]['name'])->toBe('Início');
});
