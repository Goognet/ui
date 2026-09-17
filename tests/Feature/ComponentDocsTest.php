<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Catalogue;
use Goognet\Ui\Support\ComponentProps;

/** The page loads the site's own `@vite` entries, and a package has no build of them. */
beforeEach(function (): void {
    $this->withoutVite();

    config()->set('goognet-ui.catalogue.enabled', true);
});

it('serves the component gallery outside production', function (): void {
    $this->get(route('goognet-ui.catalogue'))
        ->assertOk()
        ->assertSee('Componentes de UI')
        ->assertSee('noindex, nofollow', false);
});

it('stays closed in any environment until it is switched on', function (): void {
    config()->set('goognet-ui.catalogue.enabled');

    $this->get(route('goognet-ui.catalogue'))->assertNotFound();
});

it('renders the public page for github pages, indexable and with the install steps', function (): void {
    config()->set('goognet-ui.catalogue.public', true);
    config()->set('goognet-ui.catalogue.version', 'v9.9.9');

    $this->get(route('goognet-ui.catalogue'))
        ->assertOk()
        ->assertDontSee('noindex', false)
        ->assertSee('composer require goognet/ui')
        ->assertSee('v9.9.9')
        ->assertDontSee('apenas em dev');
});

it('renders a section for every catalogued component', function (): void {
    $rendered = (string) $this->get(route('goognet-ui.catalogue'))->getContent();

    foreach (Catalogue::entries() as $doc) {
        expect($rendered)->toContain('id="' . $doc['name'] . '"')
            ->and($rendered)->toContain($doc['title']);
    }
});

it('documents every component that lives in the ui folder', function (): void {
    $documented = collect(Catalogue::entries())->flatMap(fn (array $doc): array => $doc['sources'])->sort()->values();

    $existing = collect(glob(__DIR__ . '/../../resources/views/components/*.blade.php'))
        ->map(fn (string $path): string => basename($path, '.blade.php'))
        ->sort()
        ->values();

    expect($documented->all())->toEqual($existing->all());
});

it('reads the props of a component straight from its source', function (): void {
    $rendered = html_entity_decode((string) $this->get(route('goognet-ui.catalogue'))->getContent(), ENT_QUOTES);

    /** Defaults shown in the table come from the `@props` block, never from a copy. */
    expect($rendered)->toContain('icon-trailing')
        ->and($rendered)->toContain("'base'")
        ->and($rendered)->toContain('herdado do pai')
        ->and($rendered)->toContain('obrigatório');
});

it('runs every example in the catalogue', function (): void {
    $rendered = html_entity_decode((string) $this->get(route('goognet-ui.catalogue'))->getContent(), ENT_QUOTES);

    /** A broken example would surface as an exception page instead of the gallery. */
    foreach (Catalogue::entries() as $doc) {
        foreach ($doc['examples'] as $example) {
            expect($rendered)->toContain(trim($example['code']));
        }
    }
});

it('exercises every prop in at least one example', function (): void {
    /**
     * A prop nobody demonstrates is a prop nobody discovers. The catalogue was covering
     * 73 of 96 props when this was written; the gap is what the reader never learns the
     * component can do.
     */
    $gaps = collect(Catalogue::entries())
        ->mapWithKeys(function (array $doc): array {
            $code = collect($doc['examples'])->pluck('code')->implode("\n");

            $missing = collect($doc['sources'])
                ->flatMap(fn (string $source): array => ComponentProps::of($source))
                ->pluck('name')
                ->unique()
                /**
                 * Four ways an example can exercise a prop: as an attribute, as a bound
                 * attribute, as a bare boolean, or — for the data-driven components, where
                 * the API is an array — as a key inside it.
                 */
                ->reject(function (string $prop) use ($code): bool {
                    $quoted = preg_quote($prop, '/');

                    return preg_match('/(:?' . $quoted . '="|\s' . $quoted . '(\s|>|\n)|\'' . $quoted . '\'\s*=>)/', $code) === 1;
                })
                ->values()
                ->all();

            return $missing === [] ? [] : [$doc['name'] => $missing];
        })
        ->all();

    expect($gaps)->toBeEmpty();
});

it('never writes a raw html tag into the catalogue prose', function (): void {
    /**
     * Descriptions and notes render with `{!! !!}`, so `<dialog>` written as prose became a
     * real, unclosed element that swallowed every card after it — the modal example opened
     * a dialog with no width and nothing appeared. Tag names belong inside `<code>`, escaped.
     */
    $allowed = 'code|strong|em|br';

    $offenders = collect(Catalogue::entries())
        ->mapWithKeys(function (array $doc) use ($allowed): array {
            $found = collect([$doc['description'], ...$doc['notes'] ?? []])
                ->flatMap(function (string $text) use ($allowed): array {
                    preg_match_all('/<(?!\/?(' . $allowed . ')\b)([a-z][a-z0-9-]*)[^>]*>/i', $text, $matches);

                    return $matches[2];
                })
                ->unique()
                ->values()
                ->all();

            return $found === [] ? [] : [$doc['name'] => $found];
        })
        ->all();

    expect($offenders)->toBeEmpty();
});

it('renders the catalogue with every element closed', function (): void {
    /**
     * The W3C validator refuses this page — it is over half a megabyte — so it was the one
     * route never checked, and it is where an unclosed `<dialog>` hid. Balance is the part
     * of validity that a broken component actually breaks.
     */
    $html = (string) $this->get(route('goognet-ui.catalogue'))->getContent();

    $unbalanced = collect(['dialog', 'details', 'summary', 'section', 'header', 'nav', 'main', 'article', 'figure', 'table', 'ul', 'ol', 'li', 'button', 'form', 'fieldset'])
        ->mapWithKeys(function (string $tag) use ($html): array {
            $open  = preg_match_all('/<' . $tag . '(\s|>)/i', $html);
            $close = preg_match_all('/<\/' . $tag . '>/i', $html);

            return $open === $close ? [] : [$tag => $open . ' abertas / ' . $close . ' fechadas'];
        })
        ->all();

    expect($unbalanced)->toBeEmpty();
});

it('declares each catalogue key once per component', function (): void {
    /**
     * PHP keeps the last of two identical keys in an array literal and says nothing, so a
     * second `'notes' =>` in the same entry deletes the first one's contents on the way in.
     * That is what silently dropped the five lightbox notes from the carousel. Only the raw
     * file can show it: by the time the config is loaded, the lost block is already gone.
     */
    $source = file_get_contents(__DIR__ . '/../../resources/docs/components.php');

    $duplicates = collect(preg_split('/^ {4}\[$/m', $source))
        ->mapWithKeys(function (string $entry): array {
            preg_match_all("/^ {8}'(\w+)'\s*=>/m", $entry, $keys);

            $repeated = collect($keys[1])->duplicates()->unique()->values()->all();

            preg_match("/'name'\s*=>\s*'([^']+)'/", $entry, $name);

            return $repeated === [] ? [] : [$name[1] ?? 'desconhecido' => $repeated];
        })
        ->all();

    expect($duplicates)->toBeEmpty();
});

it('opens in production only when the config says so', function (): void {
    $this->app->detectEnvironment(fn (): string => 'production');

    config()->set('goognet-ui.catalogue.enabled', true);

    $this->get(route('goognet-ui.catalogue'))->assertOk();
});

it('stays closed outside production when switched off', function (): void {
    config()->set('goognet-ui.catalogue.enabled', false);

    $this->get(route('goognet-ui.catalogue'))->assertNotFound();
});

it('names the tags with the configured prefix', function (): void {
    config()->set('goognet-ui.prefix', 'gn-');

    expect(Catalogue::tag('button'))->toBe('gn-button')
        ->and(Catalogue::code('<x-ui.modal name="m"><x-ui.button>Ok</x-ui.button></x-ui.modal>'))
        ->toBe('<x-gn-modal name="m"><x-gn-button>Ok</x-gn-button></x-gn-modal>');
});

it('falls back to the internal name when the prefix is emptied', function (): void {
    config()->set('goognet-ui.prefix', '');

    expect(Catalogue::code('<x-ui.button>Ok</x-ui.button>'))->toBe('<x-goognet-ui::button>Ok</x-goognet-ui::button>');
});

it('inlines a logo with no active content, since it is printed unescaped', function (): void {
    $svg = Catalogue::logo();

    expect($svg)->toStartWith('<svg')
        ->and(preg_match('/<script|<foreignObject|\son[a-z]+\s*=|javascript:|<iframe|<embed|<object/i', $svg))->toBe(0);
});
