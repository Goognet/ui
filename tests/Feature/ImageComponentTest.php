<?php

declare(strict_types = 1);

use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Vite as ViteFacade;

/**
 * Fixtures stand in for what the images() plugin in vite.config.js writes during a
 * build: the component measures the source with `getimagesize` and lists the widths
 * that exist on disk. They live outside `resources/images` on purpose — a running
 * `npm run dev` watches that directory and would cut its own copies of them mid-test.
 */
const FIXTURES = 'tests/Fixtures/images/';

function writeFixture(string $name, int $width, int $height): void
{
    $canvas = imagecreatetruecolor($width, $height);

    str_ends_with($name, '.webp')
        ? imagewebp($canvas, base_path(FIXTURES . $name))
        : imagejpeg($canvas, base_path(FIXTURES . $name));

    imagedestroy($canvas);
}

beforeEach(function (): void {
    ViteFacade::clearResolvedInstance();

    $this->swap(Vite::class, new class () extends Vite
    {
        public function asset($asset, $buildDirectory = null): string
        {
            return '/' . ltrim((string) $asset, '/');
        }
    });

    if (! is_dir(base_path(FIXTURES))) {
        mkdir(base_path(FIXTURES), 0755, true);
    }

    writeFixture('fixture.jpg', 1600, 900);
    writeFixture('fixture.webp', 1600, 900);

    foreach ([400, 800] as $width) {
        writeFixture("fixture-{$width}.jpg", $width, (int) ($width * 9 / 16));
        writeFixture("fixture-{$width}.webp", $width, (int) ($width * 9 / 16));
    }
});

afterEach(function (): void {
    foreach (glob(base_path(FIXTURES . '*')) as $file) {
        unlink($file);
    }
});

it('offers webp first and keeps the original as the fallback', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" alt="Exemplo" />');

    expect($rendered)->toContain('<picture>')
        ->toContain('<source type="image/webp"')
        ->toContain('src="/tests/Fixtures/images/fixture.jpg"')
        ->toContain('alt="Exemplo"');
});

it('builds the srcset from the widths on disk and closes it with the full size', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" />');

    expect($rendered)->toContain('srcset="/tests/Fixtures/images/fixture-400.webp 400w, /tests/Fixtures/images/fixture-800.webp 800w, /tests/Fixtures/images/fixture.webp 1600w"')
        ->toContain('srcset="/tests/Fixtures/images/fixture-400.jpg 400w, /tests/Fixtures/images/fixture-800.jpg 800w, /tests/Fixtures/images/fixture.jpg 1600w"');
});

it('narrows the srcset to the widths that were asked for', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" :widths="[400]" />');

    expect($rendered)->toContain('/tests/Fixtures/images/fixture-400.webp 400w')
        ->not->toContain('fixture-800.webp');
});

it('measures the source so the box is reserved before the bytes arrive', function (): void {
    $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" />')
        ->assertSee('width="1600"', false)
        ->assertSee('height="900"', false);
});

it('carries the sizes attribute to both the source and the img', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" sizes="50vw" />');

    expect(substr_count($rendered, 'sizes="50vw"'))->toBe(2);
});

it('lazy loads by default and only claims priority when asked', function (): void {
    $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" />')
        ->assertSee('loading="lazy"', false)
        ->assertSee('decoding="async"', false)
        ->assertDontSee('fetchpriority', false);

    $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" eager />')
        ->assertSee('loading="eager"', false)
        ->assertSee('fetchpriority="high"', false);
});

it('leaves a vector as a plain img', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="logo.svg" alt="Logo" />');

    expect($rendered)->not->toContain('<picture')
        ->not->toContain('image/webp')
        ->and($rendered)->toContain('src="/resources/images/logo.svg"');
});

it('passes a remote url through untouched', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="https://example.test/foto.jpg" alt="Remota" />');

    expect($rendered)->toContain('src="https://example.test/foto.jpg"')
        ->not->toContain('<picture')
        ->not->toContain('resources/images');
});

it('stays a plain img when the file has no companions on disk', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="ausente.jpg" />');

    expect($rendered)->not->toContain('<picture')
        ->and($rendered)->toContain('src="/resources/images/ausente.jpg"');
});

it('takes a path with a slash as given', function (): void {
    $this->blade('<x-ui.image src="storage/uploads/foto.jpg" />')
        ->assertSee('src="/storage/uploads/foto.jpg"', false);
});

it('merges caller attributes onto the image, not the picture', function (): void {
    $rendered = (string) $this->blade('<x-ui.image src="tests/Fixtures/images/fixture.jpg" class="w-full rounded-lg" />');

    expect($rendered)->toMatch('/<img[^>]*class="w-full rounded-lg"/')
        ->and($rendered)->toContain('<picture>');
});
