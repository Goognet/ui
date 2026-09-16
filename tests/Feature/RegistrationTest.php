<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

/**
 * The aliases are registered when the compiler is resolved, so a fresh compiler is what reads a
 * changed prefix — the one already built for this test kept the aliases of the boot.
 *
 * @return array<string, string>
 */
function aliasesWithPrefix(?string $prefix): array
{
    config()->set('goognet-ui.prefix', $prefix);

    app()->forgetInstance('blade.compiler');
    Blade::clearResolvedInstance('blade.compiler');

    return app('blade.compiler')->getClassComponentAliases();
}

it('renders a component under the default prefix', function (): void {
    expect((string) $this->blade('<x-ui.button>Enviar</x-ui.button>'))
        ->toContain('<button')
        ->toContain('Enviar');
});

it('renders a component under its fixed internal name', function (): void {
    expect((string) $this->blade('<x-goognet-ui::button>Enviar</x-goognet-ui::button>'))
        ->toContain('<button')
        ->toContain('Enviar');
});

it('points every alias at the package view, not at a copy of it', function (): void {
    expect(aliasesWithPrefix('ui.'))->toHaveKey('ui.button', 'goognet-ui::components.button');
});

it('follows the configured prefix, separator included', function (): void {
    expect(aliasesWithPrefix('gn-'))
        ->toHaveKey('gn-button')
        ->not->toHaveKey('ui.button');
});

it('registers no alias when the prefix is emptied', function (): void {
    expect(aliasesWithPrefix(''))->not->toHaveKey('button')
        ->and(aliasesWithPrefix(null))->not->toHaveKey('button');
});

it('publishes the config and the views under their own tags', function (): void {
    $this->artisan('vendor:publish', ['--tag' => 'goognet-ui-config', '--force' => true])->assertSuccessful();

    expect(config_path('goognet-ui.php'))->toBeFile();

    unlink(config_path('goognet-ui.php'));
});

it('never references another component through the configurable prefix', function (): void {
    $offenders = collect(glob(__DIR__ . '/../../resources/views/components/*.blade.php'))
        ->filter(fn (string $file): bool => preg_match('/<\/?x-ui[.:-]/', (string) file_get_contents($file)) === 1)
        ->map(fn (string $file): string => basename($file))
        ->values()
        ->all();

    expect($offenders)->toBeEmpty();
});

it('renders the composed components by their internal names alone', function (string $template, string $expected): void {
    expect((string) $this->blade($template))->toContain($expected);
})->with([
    'modal with its close button' => ['<x-goognet-ui::modal name="m" title="T">x</x-goognet-ui::modal>', 'data-modal-close'],
    'accordion from a faq'        => ['<x-goognet-ui::accordion :faq="[\'Pergunta\' => \'Resposta\']" />', '<details'],
    'gallery item with an image'  => ['<x-goognet-ui::gallery><x-goognet-ui::gallery-item src="https://exemplo.com/a.jpg" /></x-goognet-ui::gallery>', '<img'],
    'tabs with a tab'             => ['<x-goognet-ui::tabs name="t"><x-goognet-ui::tab label="A">x</x-goognet-ui::tab></x-goognet-ui::tabs>', 'type="radio"'],
]);
