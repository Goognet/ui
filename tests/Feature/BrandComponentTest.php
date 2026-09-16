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

    config()->set('goognet-ui.company.name', 'Goognet');

    /**
     * The boilerplate ships this empty, so the component renders a lettermark. These tests are
     * about the image paths, so they name a file — the Vite fake above resolves any path, and
     * no file has to exist on disk for it.
     */
    config()->set('goognet-ui.company.logo', 'logo.svg');
});

it('defaults to logo.svg in resources/images', function (): void {
    $this->blade('<x-ui.brand />')
        ->assertSee('src="/resources/images/logo.svg"', false)
        ->assertSee('alt="Goognet"', false)
        ->assertDontSee('<picture', false);
});

it('takes the file it is given, with no naming convention in the way', function (string $logo, string $expected): void {
    /**
     * The whole point of the Flux shape: the call site names the file it wants. There is no
     * suffix rule to learn and no second file that has to exist for a first one to work.
     */
    $this->blade('<x-ui.brand logo="' . $logo . '" />')
        ->assertSee('src="' . $expected . '"', false);
})->with([
    'nome simples'   => ['logo-symbol.svg', '/resources/images/logo-symbol.svg'],
    'outra marca'    => ['logo-symbol.svg', '/resources/images/logo-symbol.svg'],
    'com barra'      => ['resources/images/brand/mark.svg', '/resources/images/brand/mark.svg'],
    'fora de images' => ['storage/marca.svg', '/storage/marca.svg'],
]);

it('renders a single image, never a scheme toggle', function (): void {
    /** Light and dark are two files, chosen by the call site — not two images and a CSS switch. */
    $rendered = (string) $this->blade('<x-ui.brand logo="logo-light.svg" />');

    expect(substr_count($rendered, '<img'))->toBe(1)
        ->and($rendered)->not->toContain('dark:hidden')
        ->and($rendered)->not->toContain('dark:block');
});

it('wraps a jpg or png in a picture with the generated webp', function (string $file, string $webp): void {
    $this->blade('<x-ui.brand logo="' . $file . '" />')
        ->assertSee('<picture>', false)
        ->assertSee('srcset="/resources/images/' . $webp . '" type="image/webp"', false)
        ->assertSee('src="/resources/images/' . $file . '"', false);
})->with([
    ['logo.png', 'logo.webp'],
    ['logo.jpg', 'logo.webp'],
    ['logo.jpeg', 'logo.webp'],
    ['logo.PNG', 'logo.webp'],
]);

it('leaves an svg outside of picture', function (): void {
    $this->blade('<x-ui.brand logo="logo-light.svg" />')
        ->assertDontSee('<picture', false)
        ->assertDontSee('image/webp', false);
});

it('merges attributes onto the image, not the picture', function (): void {
    $this->blade('<x-ui.brand logo="logo.png" class="h-8 w-auto" loading="eager" />')
        ->assertSee('class="h-8 w-auto"', false)
        ->assertSee('loading="eager"', false)
        ->assertSee('<picture>', false);
});

it('falls back to the company name for the alt text and allows an override', function (): void {
    $this->blade('<x-ui.brand />')->assertSee('alt="Goognet"', false);

    $this->blade('<x-ui.brand alt="Voltar ao início" />')
        ->assertSee('alt="Voltar ao início"', false)
        ->assertDontSee('alt="Goognet"', false);
});

it('renders no anchor when no href is given', function (): void {
    expect((string) $this->blade('<x-ui.brand />'))->not->toContain('<a ');
});

it('wraps the logo in an anchor when given an href', function (): void {
    $rendered = (string) $this->blade('<x-ui.brand href="/" class="h-8 w-auto" />');

    /** By attribute and not by the whole tag: the order they are written in is not the contract. */
    expect($rendered)->toMatch('/<a [^>]*href="\/"/')
        ->toMatch('/<a [^>]*class="inline-flex"/')
        ->toContain('</a>')
        ->toContain('class="h-8 w-auto"');
});

it('wraps a picture too, not just a bare img', function (): void {
    $rendered = (string) $this->blade('<x-ui.brand logo="logo.png" href="/" />');

    expect($rendered)->toMatch('/<a [^>]*>\s*<picture>/')
        ->and(substr_count($rendered, '<a '))->toBe(1);
});

it('keeps the sizing class on the image, not on the anchor', function (): void {
    $rendered = (string) $this->blade('<x-ui.brand href="/" class="h-8 w-auto" />');

    expect($rendered)->toMatch('/<a [^>]*class="inline-flex"[^>]*>/')
        ->and($rendered)->toMatch('/<img[^>]*class="h-8 w-auto"/')
        /** The point of the test: the sizing never leaks onto the anchor. */
        ->and($rendered)->not->toMatch('/<a [^>]*h-8/');
});

it('guards an external brand link', function (): void {
    $this->blade('<x-ui.brand href="https://goognet.com.br" external />')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('adds the rel guard when target blank comes through as an attribute', function (): void {
    $this->blade('<x-ui.brand href="https://goognet.com.br" target="_blank" />')
        ->assertSee('rel="noopener noreferrer"', false);
});

it('never targets a brand without an href', function (): void {
    expect((string) $this->blade('<x-ui.brand external />'))->not->toContain('target=');
});

it('names the brand link through the image alt text', function (): void {
    $this->blade('<x-ui.brand href="/" />')->assertSee('alt="Goognet"', false);
});

it('sets the name beside the mark', function (): void {
    $rendered = (string) $this->blade('<x-ui.brand logo="logo-symbol.svg" name="Acme Inc." class="size-8" />');

    expect($rendered)
        ->toContain('Acme Inc.')
        ->toContain('logo-symbol.svg')
        ->toContain('items-center gap-2.5');
});

it('silences the mark once the name is spelled out', function (): void {
    /**
     * With the words right there, the image is decoration. An `alt` repeating them makes a
     * screen reader announce the company twice in a row.
     */
    expect((string) $this->blade('<x-ui.brand name="Acme Inc." />'))
        ->toContain('alt=""')
        ->not->toContain('alt="Acme Inc."');
});

it('keeps an explicit alt even beside a name', function (): void {
    /** For a mark that carries something the name does not. */
    expect((string) $this->blade('<x-ui.brand name="Acme" alt="Selo de 20 anos" />'))
        ->toContain('alt="Selo de 20 anos"');
});

it('changes nothing at all without a name', function (): void {
    /**
     * The wrapper exists to hold two things together. With one thing there is nothing to hold,
     * and every call site already in the project keeps the markup it had.
     */
    expect(trim((string) $this->blade('<x-ui.brand class="h-7 w-auto" />')))
        ->toStartWith('<img')
        ->not->toContain('<span');
});

it('takes markup through the logo slot, under the same name as the prop', function (): void {
    /**
     * Flux's shape: `logo` is a path when written as an attribute and markup when it arrives as
     * a slot. Blade hands both over in the same variable, so the component tells them apart by
     * type — and the slot wins when somebody passes both.
     */
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.brand logo="logo.svg" name="Launchpad">
            <x-slot:logo class="size-7 rounded-lg bg-primary">GN</x-slot:logo>
        </x-ui.brand>
        BLADE);

    expect($rendered)
        ->not->toContain('<img')
        ->not->toContain('logo.svg')
        ->toContain('GN')
        /** The slot's own classes reach the box, so the call site sizes it. */
        ->toContain('size-7 rounded-lg bg-primary')
        ->toContain('Launchpad');
});

it('wraps the slot and the name in the anchor when linked', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.brand href="/" name="Acme">
            <x-slot:logo>GN</x-slot:logo>
        </x-ui.brand>
        BLADE);

    expect($rendered)->toMatch('/<a [^>]*href="\/"/')
        ->and(substr_count($rendered, '<a '))->toBe(1)
        ->and(substr_count($rendered, '</a>'))->toBe(1);
});

it('takes the logo from the global config', function (): void {
    /**
     * One name for the file, so the JSON-LD `logo`, the header and the footer cannot drift
     * apart. Before this the component hardcoded `logo.svg` while the config named it a second
     * time — changing one left the other behind.
     */
    config()->set('goognet-ui.company.logo', 'logo-symbol.svg');

    expect((string) $this->blade('<x-ui.brand />'))->toContain('logo-symbol.svg');
});

it('falls back to the company name when no logo is configured', function (): void {
    /**
     * The boilerplate versions no logo, so this is its normal state, not a half-built site.
     * A file name as the floor would throw the moment the file is not on disk — which is
     * exactly what took every page down once the placeholder SVGs were deleted.
     */
    config()->set('goognet-ui.company.logo', '');

    expect((string) $this->blade('<x-ui.brand />'))
        ->toContain('Goognet')
        ->not->toContain('<img')
        ->not->toContain('logo.svg');
});

it('takes a remote url as the src, with no picture around it', function (): void {
    /** `Vite::asset()` throws on a URL instead of resolving it, so the path has to skip it. */
    expect((string) $this->blade('<x-ui.brand logo="https://cdn.exemplo.com/marca.svg" alt="Marca" />'))
        ->toContain('src="https://cdn.exemplo.com/marca.svg"')
        ->not->toContain('<picture');
});

it('never wraps a remote png in a picture, having no sibling on this disk', function (): void {
    expect((string) $this->blade('<x-ui.brand logo="https://cdn.exemplo.com/marca.png" alt="Marca" />'))
        ->not->toContain('<picture')
        ->not->toContain('.webp');
});

it('puts the call site attributes on the wrapper when no image is drawn', function (): void {
    /** They used to go to the `<img>`, which the slot and the lettermark never render: dropped in silence. */
    config()->set('goognet-ui.company.logo', '');

    expect((string) $this->blade('<x-ui.brand class="h-7 shrink-0" />'))
        ->toContain('h-7 shrink-0')
        ->and((string) $this->blade('<x-ui.brand name="Acme" class="h-7 shrink-0"><x-slot:logo>I</x-slot:logo></x-ui.brand>'))->toContain('h-7 shrink-0');
});

it('lets the call site override the configured logo', function (): void {
    config()->set('goognet-ui.company.logo', 'logo.svg');

    expect((string) $this->blade('<x-ui.brand logo="logo-light.svg" />'))
        ->toContain('logo-light.svg')
        ->not->toContain('/logo.svg');
});
