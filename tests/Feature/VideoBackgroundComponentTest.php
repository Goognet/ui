<?php

declare(strict_types = 1);

use Illuminate\Foundation\Vite;
use Illuminate\Foundation\ViteException;
use Illuminate\Support\Facades\Vite as ViteFacade;
use Illuminate\View\ViewException;

/**
 * The fake stands in for the Vite manifest: it answers for the files it was given and
 * fails for the rest, exactly as `Vite::asset()` does once a build has run.
 */
function fakeManifest(array $known): void
{
    ViteFacade::clearResolvedInstance();

    $fake = new class () extends Vite
    {
        /** @var array<int, string> */
        public array $known = [];

        public function asset($asset, $buildDirectory = null): string
        {
            $asset = (string) $asset;

            if (! in_array($asset, $this->known, true)) {
                throw new ViteException("Unable to locate file in Vite manifest: {$asset}.");
            }

            return '/' . ltrim($asset, '/');
        }
    };

    $fake->known = $known;

    /**
     * The container directly, not `test()->swap()`. That helper lives on the TestCase while
     * `test()` is typed as the pending call, so static analysis cannot see it — and `swap()`
     * is only a thin wrapper over this same call.
     */
    app()->instance(Vite::class, $fake);
}

beforeEach(function (): void {
    fakeManifest([
        'resources/videos/fundo.webm',
        'resources/videos/fundo.h264.mp4',
        'resources/images/poster.jpg',
    ]);
});

it('offers webm before mp4', function (): void {
    $rendered = (string) $this->blade('<x-ui.video-background src="fundo" />');

    expect($rendered)->toContain('type="video/webm"')
        ->toContain('type="video/mp4"')
        ->and(strpos($rendered, 'video/webm'))->toBeLessThan(strpos($rendered, 'video/mp4'));
});

it('takes the master name and reaches for what the build writes beside it', function (): void {
    /** `fundo.mp4` goes into the pipeline; `fundo.webm` and `fundo.h264.mp4` come out. */
    $this->blade('<x-ui.video-background src="fundo.mp4" />')
        ->assertSee('src="/resources/videos/fundo.webm"', false)
        ->assertSee('src="/resources/videos/fundo.h264.mp4"', false);
});

it('never offers the master itself', function (): void {
    expect((string) $this->blade('<x-ui.video-background src="fundo" />'))
        ->not->toContain('src="/resources/videos/fundo.mp4"');
});

it('lets the Vite error through instead of leaving a black section', function (): void {
    /** Swallowing this was the bug: the build failed and the page said nothing. */
    expect(fn () => $this->blade('<x-ui.video-background src="nao-existe" />'))
        ->toThrow(ViewException::class, 'Unable to locate file in Vite manifest: resources/videos/nao-existe.webm');
});

it('carries the attributes that autoplay actually needs', function (): void {
    /** Without `playsinline` iOS Safari refuses to play inline; without `muted` every
     *  browser refuses to autoplay at all. */
    $this->blade('<x-ui.video-background src="fundo" />')
        ->assertSee('autoplay', false)
        ->assertSee('muted', false)
        ->assertSee('playsinline', false)
        ->assertSee('preload="metadata"', false);
});

it('hides the video from assistive tech and the tab order', function (): void {
    $rendered = (string) $this->blade('<x-ui.video-background src="fundo" />');

    expect($rendered)->toContain('aria-hidden="true"')
        ->toContain('tabindex="-1"');
});

it('loops unless told not to', function (): void {
    expect((string) $this->blade('<x-ui.video-background src="fundo" />'))->toContain('loop')
        ->and((string) $this->blade('<x-ui.video-background src="fundo" :loop="false" />'))->not->toContain('loop');
});

it('opens its own stacking context so the video cannot slip behind the page', function (): void {
    $this->blade('<x-ui.video-background src="fundo" />')->assertSee('isolate', false);
});

it('puts the poster on the video and behind it', function (): void {
    $rendered = (string) $this->blade('<x-ui.video-background src="fundo" poster="poster.jpg" />');

    expect($rendered)->toContain('poster="/resources/images/poster.jpg"')
        ->toContain("background-image: url('/resources/images/poster.jpg')");
});

it('takes a scrim class and drops the layer when told to', function (): void {
    $this->blade('<x-ui.video-background src="fundo" overlay="bg-primary/50" />')
        ->assertSee('bg-primary/50', false);

    expect((string) $this->blade('<x-ui.video-background src="fundo" :overlay="false" />'))
        ->not->toContain('-z-10');
});

it('renders the slot above every layer', function (): void {
    $rendered = (string) $this->blade('<x-ui.video-background src="fundo">Chamada</x-ui.video-background>');

    expect($rendered)->toContain('Chamada')
        ->and(strpos($rendered, 'Chamada'))->toBeGreaterThan(strpos($rendered, '<video'));
});

it('keeps the caller classes and the height default', function (): void {
    $this->blade('<x-ui.video-background src="fundo" class="flex items-center" />')
        ->assertSee('flex items-center', false)
        ->assertSee('h-svh', false);
});
