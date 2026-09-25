<?php

declare(strict_types = 1);

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

function uiLangPath(string $locale): string
{
    return __DIR__ . '/../../lang/' . $locale . '/ui.php';
}

/**
 * @return list<string>
 */
function uiLangKeys(string $locale): array
{
    $lines = require uiLangPath($locale);

    return collect(Arr::dot($lines))->keys()->sort()->values()->all();
}

/**
 * Every `goognet-ui::ui.*` key the components actually ask for.
 *
 * @return list<string>
 */
function uiKeysInUse(): array
{
    $views = collect(glob(__DIR__ . '/../../resources/views/components/*.blade.php') ?: [])
        ->map(fn (string $path): string => (string) file_get_contents($path))
        ->implode("\n");

    preg_match_all("/goognet-ui::ui\.([a-z.]+)/", $views, $matches);

    return collect($matches[1])->unique()->sort()->values()->all();
}

it('ships the same keys in every language', function (): void {
    $portuguese = uiLangKeys('pt_BR');

    expect(uiLangKeys('en'))->toBe($portuguese)
        ->and(uiLangKeys('es'))->toBe($portuguese);
});

it('translates every key the components ask for, in every language', function (string $locale): void {
    /**
     * A key with no line behind it renders as the key itself — `goognet-ui::ui.modal.close`
     * printed on the button. Nothing else in the suite would notice.
     */
    $missing = collect(uiKeysInUse())
        ->reject(fn (string $key): bool => in_array($key, uiLangKeys($locale), true))
        ->all();

    expect($missing)->toBeEmpty();
})->with(['pt_BR', 'en', 'es']);

it('leaves no key behind that nothing asks for', function (): void {
    $unused = collect(uiLangKeys('pt_BR'))
        ->reject(fn (string $key): bool => in_array($key, uiKeysInUse(), true))
        ->all();

    expect($unused)->toBeEmpty();
});

it('follows the locale the request runs in', function (string $locale, string $close, string $accept): void {
    app()->setLocale($locale);

    expect((string) $this->blade('<x-ui.modal name="m" title="T">corpo</x-ui.modal>'))->toContain($close)
        ->and((string) $this->blade('<x-ui.cookie-consent />'))->toContain($accept);
})->with([
    'português' => ['pt_BR', 'Fechar', 'Aceitar'],
    'inglês'    => ['en', 'Close', 'Accept'],
    'espanhol'  => ['es', 'Cerrar', 'Aceptar'],
]);

it('falls back to english for a language the package does not carry', function (): void {
    app()->setLocale('fr');

    expect((string) $this->blade('<x-ui.modal name="m" title="T">corpo</x-ui.modal>'))->toContain('Close');
});

it('lets the site replace a line without forking the component', function (): void {
    Lang::addLines(['ui.modal.close' => 'Dispensar'], 'pt_BR', 'goognet-ui');

    expect((string) $this->blade('<x-ui.modal name="m" title="T">corpo</x-ui.modal>'))
        ->toContain('Dispensar')
        ->not->toContain('>Fechar<');
});

it('puts the paginator figures inside the sentence, not around it', function (): void {
    app()->setLocale('es');

    $paginator = new LengthAwarePaginator(range(1, 10), 300, 10, 7, ['path' => '/articulos']);

    expect((string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator]))
        ->toContain('Mostrando')
        ->toContain('>61</span>')
        ->toContain('>300</span>')
        ->toContain('aria-label="Ir a la página 8"')
        ->toContain('aria-label="Paginación"');
});

it('emphasises the cookie term wherever the sentence puts it', function (): void {
    app()->setLocale('en');

    expect((string) $this->blade('<x-ui.cookie-consent />'))
        ->toContain('<b class="font-semibold">essential cookies</b>')
        ->toContain('We use only');
});

it('never lets a translated line smuggle markup into the cookie notice', function (): void {
    Lang::addLines(['ui.cookie.essential' => '<script>alert(1)</script>'], 'pt_BR', 'goognet-ui');

    expect((string) $this->blade('<x-ui.cookie-consent />'))
        ->not->toContain('<script>alert(1)</script>')
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
});

it('builds the public catalogue in the language it is written in', function (): void {
    /**
     * `bin/docs` boots its own application, outside this suite, and a fresh Laravel defaults
     * to `en` — which silently published a Portuguese page with English buttons on it. Only
     * the script can say which locale it picks.
     */
    $script = (string) file_get_contents(__DIR__ . '/../../bin/docs');

    expect($script)->toContain("'app.locale'");
    expect($script)->toContain("'pt_BR'");
});
