<?php

declare(strict_types = 1);

it('draws a neutral alert and lets the call site paint it', function (): void {
    $html = (string) $this->blade('<x-ui.alert class="border-green-200 bg-green-50 text-green-800">Pronto</x-ui.alert>');

    expect($html)->toContain('bg-green-50')
        ->toContain('text-green-800')
        ->not->toContain('bg-white')
        ->not->toContain('text-neutral-700');
});

it('only announces itself when it appears after an action', function (): void {
    expect((string) $this->blade('<x-ui.alert>Aviso fixo</x-ui.alert>'))->not->toContain('role="alert"');

    expect((string) $this->blade('<x-ui.alert live>Enviado</x-ui.alert>'))->toContain('role="alert"');
});

it('marks itself for the script only when it can be dismissed', function (): void {
    expect((string) $this->blade('<x-ui.alert>Fixo</x-ui.alert>'))
        ->not->toContain('data-alert')
        ->not->toContain('Fechar aviso');

    expect((string) $this->blade('<x-ui.alert dismissible>Some daqui</x-ui.alert>'))
        ->toContain('data-alert')
        ->toContain('data-alert-dismiss')
        ->toContain('Fechar aviso');
});

it('separates the title from the body only when both are there', function (): void {
    expect((string) $this->blade('<x-ui.alert title="Erro">Tente de novo</x-ui.alert>'))->toContain('mt-1');

    expect((string) $this->blade('<x-ui.alert>Tente de novo</x-ui.alert>'))->not->toContain('mt-1');
});
