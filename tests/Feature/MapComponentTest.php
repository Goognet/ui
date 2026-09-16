<?php

declare(strict_types = 1);

const EMBED = 'https://www.google.com/maps/embed?pb=!1m18!1m12';

it('falls back to the configured address', function (): void {
    config(['goognet-ui.location.map' => EMBED]);

    expect((string) $this->blade('<x-ui.map />'))->toContain('src="' . EMBED . '"');
});

it('renders nothing at all when no address is set', function (): void {
    /**
     * `<iframe src="">` is not an empty frame: the browser resolves the empty string against
     * the current document and loads the page inside its own box. A site that has not filled
     * in LOCATION_MAP_LINK would show a copy of itself, so the element never reaches the DOM.
     */
    config(['goognet-ui.location.map' => '']);

    expect(trim((string) $this->blade('<x-ui.map />')))->toBeEmpty();
});

it('reserves the frame height before the tiles arrive', function (): void {
    /** Google's embed has no intrinsic size; with no ratio the box is zero high until load. */
    expect((string) $this->blade('<x-ui.map :src="\'' . EMBED . '\'" />'))->toContain('aspect-video');
});

it('takes a named ratio, an arbitrary one, or none', function (mixed $ratio, string $expected): void {
    $rendered = (string) $this->blade('<x-ui.map :src="$src" :ratio="$ratio" />', ['src' => EMBED, 'ratio' => $ratio]);

    $classes = str($rendered)->after('class="')->before('"')->toString();

    expect($classes)->toContain($expected);
})->with([
    'padrão'     => ['video', 'aspect-video'],
    'quadrado'   => ['square', 'aspect-square'],
    'arbitrário' => ['aspect-[4/3]', 'aspect-[4/3]'],
    /** A name that is not a key has to already be a utility; a bare value would emit junk. */
    'inválido' => ['banana', 'aspect-video'],
]);

it('lets the parent own the height', function (): void {
    $rendered = (string) $this->blade('<x-ui.map :src="$src" :ratio="false" class="size-full" />', ['src' => EMBED]);

    expect($rendered)
        ->not->toContain('aspect-')
        ->toContain('size-full');
});

it('names the frame for assistive tech', function (): void {
    /** An `<iframe>` without a title is a frame a screen reader cannot describe, and the W3C rejects it. */
    expect((string) $this->blade('<x-ui.map :src="$src" />', ['src' => EMBED]))
        ->toContain('title="Mapa de localização"')
        ->and((string) $this->blade('<x-ui.map :src="$src" title="Loja da Paulista" />', ['src' => EMBED]))
        ->toContain('title="Loja da Paulista"');
});

it('defers the third-party request unless the map leads the page', function (): void {
    expect((string) $this->blade('<x-ui.map :src="$src" />', ['src' => EMBED]))->toContain('loading="lazy"')
        ->and((string) $this->blade('<x-ui.map :src="$src" eager />', ['src' => EMBED]))->toContain('loading="eager"');
});

it('escapes an address instead of writing it raw', function (): void {
    /**
     * The shape this replaces echoed the value straight into the attribute. Blade closes the
     * hole by default, and this pins it: a quote in the URL must not end the attribute.
     */
    $rendered = (string) $this->blade('<x-ui.map :src="$src" />', ['src' => 'https://www.google.com/maps/embed?pb="><script>alert(1)</script>']);

    expect($rendered)
        ->not->toContain('<script>')
        ->toContain('&quot;');
});

it('keeps the referrer policy the embed documents', function (): void {
    expect((string) $this->blade('<x-ui.map :src="$src" />', ['src' => EMBED]))
        ->toContain('referrerpolicy="no-referrer-when-downgrade"')
        ->toContain('allowfullscreen');
});
