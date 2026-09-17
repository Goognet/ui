<?php

declare(strict_types = 1);

it('hands itself to the menu script instead of carrying one of its own', function (): void {
    $html = (string) $this->blade('<x-ui.dropdown label="Ações"><a href="/editar">Editar</a></x-ui.dropdown>');

    expect($html)->toContain('data-menu')
        ->toContain('data-menu-dropdown')
        ->toContain('data-menu-dropdown-panel')
        ->toContain('aria-expanded="false"');
});

it('points the trigger at the panel it opens', function (): void {
    $html = (string) $this->blade('<x-ui.dropdown label="Ações">itens</x-ui.dropdown>');

    preg_match('/aria-controls="([^"]+)"/', $html, $trigger);

    expect($html)->toContain('id="' . $trigger[1] . '"');
});

it('gives two dropdowns on the same page ids of their own', function (): void {
    $html = (string) $this->blade('<x-ui.dropdown label="A">a</x-ui.dropdown><x-ui.dropdown label="B">b</x-ui.dropdown>');

    preg_match_all('/aria-controls="([^"]+)"/', $html, $matches);

    expect($matches[1])->toHaveCount(2)
        ->and($matches[1][0])->not->toBe($matches[1][1]);
});

it('hangs the panel from the side it was told to', function (): void {
    expect((string) $this->blade('<x-ui.dropdown label="A">a</x-ui.dropdown>'))->toContain('start-0');

    expect((string) $this->blade('<x-ui.dropdown label="A" align="end">a</x-ui.dropdown>'))->toContain('end-0');
});

it('takes a trigger of its own', function (): void {
    $blade = <<<'BLADE'
        <x-ui.dropdown>
            <x-slot:trigger>
                <button type="button" data-menu-dropdown data-state="closed" aria-controls="meu-painel" aria-expanded="false">Abrir</button>
            </x-slot:trigger>

            itens
        </x-ui.dropdown>
        BLADE;

    expect((string) $this->blade($blade))->toContain('Abrir')
        ->not->toContain('heroicon-m-chevron-down');
});
