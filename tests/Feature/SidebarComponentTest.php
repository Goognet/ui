<?php

declare(strict_types = 1);

use Illuminate\View\ViewException;

it('takes the anchor map a page already has', function (): void {
    $rendered = (string) $this->blade(
        '<x-ui.sidebar label="Seções" :items="$items" />',
        ['items' => ['cookies' => 'Cookies', 'contato' => 'Contato']],
    );

    expect($rendered)
        ->toContain('href="#cookies"')
        ->toContain('Cookies')
        ->toContain('href="#contato"');
});

it('takes the label and url shape the config already uses', function (): void {
    /** Same shape as `config('goognet-ui.menu')` and `x-ui.menu`, so nothing has to be rebuilt. */
    $rendered = (string) $this->blade(
        '<x-ui.sidebar label="Atalhos" :items="$items" />',
        ['items' => [['label' => 'Início', 'url' => '/'], ['label' => 'Política', 'url' => '/politica']]],
    );

    expect($rendered)->toContain('href="/"')->toContain('href="/politica"');
});

it('tracks the reading position only for anchors on this page', function (): void {
    /**
     * A list of URLs is navigation between pages, and there the address decides what is current,
     * not the scroll position — so the script is not asked to watch it.
     */
    $anchors = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" />', ['i' => ['um' => 'Um']]);

    $pages = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" />', ['i' => [['label' => 'Um', 'url' => '/um']]]);

    expect($anchors)->toContain('data-sidebar')
        ->and($pages)->not->toContain('data-sidebar');
});

it('demands a name for the navigation', function (): void {
    /** A page carries several; a screen reader lists them by this label. */
    expect(fn () => $this->blade('<x-ui.sidebar :items="$i" label="" />', ['i' => ['um' => 'Um']]))
        ->toThrow(ViewException::class, 'exige um label');
});

it('pins below the bar, not under it', function (): void {
    /**
     * `--navbar-height` is published at run time by navbar.js, because the bar's height depends
     * on whether the site filled in the info strip. A fixed offset left the rail behind it.
     */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" />', ['i' => ['um' => 'Um']]);

    expect($rendered)->toContain('lg:top-[calc(var(--navbar-height,0px)+1.5rem)]');
});

it('lets a call site opt out of pinning', function (): void {
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" :sticky="false" />', ['i' => ['um' => 'Um']]);

    expect($rendered)->not->toContain('lg:sticky');
});

it('renders nothing when there is nothing to index', function (): void {
    expect(trim((string) $this->blade('<x-ui.sidebar label="A" :items="[]" />')))->toBeEmpty();
});

it('drops an entry that carries no destination', function (): void {
    $rendered = (string) $this->blade(
        '<x-ui.sidebar label="A" :items="$i" />',
        ['i' => [['label' => 'Sem url'], ['label' => 'Com url', 'url' => '/ok']]],
    );

    expect($rendered)->toContain('Com url')->not->toContain('Sem url');
});

it('drops the eyebrow when the page does not want one', function (): void {
    /**
     * `title=""`, not `:title="null"`. Blade compiles `@props` defaults to
     * `$$__key = $$__key ?? $__value`, so passing null explicitly falls straight back to the
     * default — an empty string is the only value that clears one.
     */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" title="" />', ['i' => ['um' => 'Um']]);

    expect($rendered)->not->toContain('Nesta página');
});

it('keeps the default eyebrow when null is passed, as Blade does', function (): void {
    /** Pinned on purpose: it looks like a bug at the call site, and it is Blade's prop semantics. */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" :title="null" />', ['i' => ['um' => 'Um']]);

    expect($rendered)->toContain('Nesta página');
});

it('is an aside holding a named nav', function (): void {
    $rendered = (string) $this->blade('<x-ui.sidebar label="Seções desta política" :items="$i" />', ['i' => ['um' => 'Um']]);

    expect($rendered)
        ->toContain('<aside')
        ->toContain('<nav')
        ->toContain('aria-label="Seções desta política"');
});

it('caps the pinned rail at the screen and scrolls it inside itself', function (): void {
    /**
     * A pinned rail taller than the viewport has no way to reach its own foot: the page scrolls,
     * the rail does not move with it, and the last entries stay permanently below the fold.
     * Measured on the catalogue, the list passed the screen at 22 entries and the last one sat
     * 76px out of reach. `svh`, so a mobile browser's collapsing toolbar does not cut it.
     */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" />', ['i' => ['um' => 'Um']]);

    expect($rendered)
        ->toContain('lg:max-h-[calc(100svh-var(--navbar-height,0px)-3rem)]')
        ->toContain('overflow-y-auto')
        /** Without it the flex child refuses to shrink below its content and the cap does nothing. */
        ->toContain('min-h-0');
});

it('leaves the overflow off when the rail is not pinned', function (): void {
    /** Nothing to escape from: an unpinned rail scrolls away with the page like any other block. */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$i" :sticky="false" />', ['i' => ['um' => 'Um']]);

    expect($rendered)->not->toContain('overflow-y-auto')->not->toContain('max-h-');
});

it('takes a route name as well as a path', function (): void {
    /** Same pair as `x-ui.menu`, for the same reason: a path in config drifts, a name does not. */
    $rendered = (string) $this->blade('<x-ui.sidebar label="A" :items="$items" />', ['items' => [
        ['label' => 'Início', 'route' => 'home'],
        ['label' => 'Privacidade', 'route' => 'privacy'],
    ]]);

    expect($rendered)
        ->toContain('href="' . route('home') . '"')
        ->toContain('href="' . route('privacy') . '"')
        /** Page addresses, not anchors, so the scroll spy is not asked to watch them. */
        ->not->toContain('data-sidebar');
});

it('names the missing route and the item that asked for it', function (): void {
    expect(fn () => $this->blade('<x-ui.sidebar label="A" :items="$items" />', ['items' => [
        ['label' => 'Blog', 'route' => 'blog.index'],
    ]]))->toThrow(ViewException::class, 'rota "blog.index", que não existe. Item: "Blog"');
});
