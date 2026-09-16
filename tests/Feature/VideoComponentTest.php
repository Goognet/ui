<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Youtube;
use Illuminate\View\ViewException;

const VIDEO_ID = 'dQw4w9WgXcQ';

it('reads every address YouTube hands out', function (string $url): void {
    /**
     * Reading only the query string missed `youtu.be`, which is what the share sheet copies —
     * the single most common way a link reaches a page.
     */
    expect(Youtube::id($url))->toBe(VIDEO_ID);
})->with([
    'watch'   => ['https://www.youtube.com/watch?v=' . VIDEO_ID],
    'curto'   => ['https://youtu.be/' . VIDEO_ID],
    'curto+t' => ['https://youtu.be/' . VIDEO_ID . '?t=42'],
    'embed'   => ['https://www.youtube.com/embed/' . VIDEO_ID],
    'shorts'  => ['https://www.youtube.com/shorts/' . VIDEO_ID],
    'live'    => ['https://www.youtube.com/live/' . VIDEO_ID],
    'lista'   => ['https://www.youtube.com/watch?list=PL1&v=' . VIDEO_ID],
    'id puro' => [VIDEO_ID],
]);

it('refuses what is not a video', function (?string $url): void {
    expect(Youtube::id($url))->toBeNull();
})->with([
    'outro site'   => ['https://vimeo.com/12345'],
    'sem id'       => ['https://www.youtube.com/watch?list=PL123'],
    'só o domínio' => ['https://www.youtube.com'],
    'id curto'     => ['https://youtu.be/abc'],
    'vazio'        => [''],
    'nulo'         => [null],
    'só espaços'   => ['   '],
]);

it('never asks YouTube anything while rendering', function (): void {
    /**
     * The shape this replaces called `get_headers()` up to four times per video, per render,
     * uncached. This pins the contract: the class is string work, so a slow YouTube cannot
     * become a slow page.
     */
    /** Comments are stripped first: the docblock names `get_headers()` to say what it replaced. */
    $code = collect(token_get_all(file_get_contents(__DIR__ . '/../../src/Support/Youtube.php')))
        ->reject(fn (array | string $token): bool => is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true))
        ->map(fn (array | string $token): string => is_array($token) ? $token[1] : $token)
        ->implode('');

    expect($code)
        ->not->toContain('get_headers')
        ->not->toContain('file_get_contents')
        ->not->toContain('curl_')
        ->and(Youtube::thumbnail(VIDEO_ID))->toBe('https://i.ytimg.com/vi/' . VIDEO_ID . '/maxresdefault.jpg');
});

it('throws on an address it cannot read', function (): void {
    /**
     * A wrong address is the page author's mistake, not run-time state, so it fails loudly the
     * way `x-ui.modal` and `x-ui.tabs` do. Blade wraps whatever a component throws in a
     * `ViewException`, so the message is what the assertion can hold on to.
     */
    expect(fn () => $this->blade('<x-ui.video url="https://vimeo.com/12345" />'))
        ->toThrow(ViewException::class, 'não reconheceu um vídeo do YouTube');
});

it('opens in the lightbox by default and marks the source type', function (): void {
    $rendered = (string) $this->blade('<x-ui.video url="https://youtu.be/' . VIDEO_ID . '" />');

    expect($rendered)
        ->toContain('data-fslightbox="video-' . VIDEO_ID . '"')
        ->toContain('data-type="youtube"')
        ->toContain('href="https://www.youtube.com/watch?v=' . VIDEO_ID . '"');
});

it('leaves for YouTube safely when the lightbox is off', function (): void {
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" :lightbox="false" />');

    expect($rendered)
        ->not->toContain('data-fslightbox')
        ->toContain('target="_blank"')
        /** `noopener` or the opened tab can reach back through `window.opener`. */
        ->toContain('rel="noopener noreferrer"');
});

it('carries a fallback poster the browser can swap in', function (): void {
    /** `maxresdefault` exists only for uploads that were 720p or better; `hqdefault` always does. */
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" />');

    expect($rendered)
        ->toContain('src="https://i.ytimg.com/vi/' . VIDEO_ID . '/maxresdefault.jpg"')
        ->toContain('data-video-poster="https://i.ytimg.com/vi/' . VIDEO_ID . '/hqdefault.jpg"');
});

it('leaves out the fallback when the quality asked for always exists', function (): void {
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" quality="high" />');

    expect($rendered)
        ->toContain('hqdefault.jpg')
        ->not->toContain('data-video-poster');
});

it('names the link with text, not with the poster alt', function (): void {
    /**
     * The shape this replaces put the video URL in the `alt` by copy-paste, so a screen reader
     * read out an address. The poster is decorative; the link carries the name.
     */
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" title="Nosso atendimento" />');

    expect($rendered)
        ->toContain('Assistir ao vídeo: Nosso atendimento')
        ->toContain('alt=""')
        ->not->toContain('alt="https://');
});

it('reserves the frame so the poster cannot shift the page', function (): void {
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" />');

    expect($rendered)
        ->toContain('aspect-video')
        ->toContain('width="1280"')
        ->toContain('height="720"');
});

it('honours a request for less motion', function (): void {
    /** Auto-starting motion that never stops is what WCAG 2.2.2 asks you to let people turn off. */
    $rendered = (string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" />');

    expect($rendered)
        ->toContain('motion-reduce:')
        ->not->toContain('animate-ping');
});

it('defers the poster unless the video leads the page', function (): void {
    expect((string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" />'))->toContain('loading="lazy"')
        ->and((string) $this->blade('<x-ui.video url="' . VIDEO_ID . '" eager />'))->toContain('loading="eager"');
});
