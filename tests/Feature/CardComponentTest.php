<?php

declare(strict_types = 1);

it('is a div until it is given somewhere to go', function (): void {
    expect((string) $this->blade('<x-ui.card>Texto</x-ui.card>'))->toStartWith('<div')
        ->and((string) $this->blade('<x-ui.card href="/servicos">Texto</x-ui.card>'))->toStartWith('<a')
        ->toContain('href="/servicos"');
});

it('lifts only when it leads somewhere', function (): void {
    expect((string) $this->blade('<x-ui.card href="/x">t</x-ui.card>'))->toContain('hover:-translate-y-0.5')
        ->and((string) $this->blade('<x-ui.card>t</x-ui.card>'))->not->toContain('hover:-translate-y-0.5');
});

it('refuses an address the library would not follow', function (): void {
    $html = (string) $this->blade('<x-ui.card href="javascript:alert(1)" target="_blank">t</x-ui.card>');

    expect($html)->toStartWith('<div')
        ->not->toContain('javascript:')
        ->not->toContain('target=');
});

it('pulls the media out to the card edge, matching the padding', function (string $padding, string $pull): void {
    $blade = <<<BLADE
        <x-ui.card padding="{$padding}">
            <x-slot:media>imagem</x-slot:media>
            corpo
        </x-ui.card>
        BLADE;

    expect((string) $this->blade($blade))->toContain($pull);
})->with([
    ['sm', '-m-4 mb-4'],
    ['base', '-m-5 mb-5'],
    ['lg', '-m-8 mb-8'],
]);

it('renders the header and the footer only when they are given', function (): void {
    $blade = <<<'BLADE'
        <x-ui.card>
            <x-slot:header>Título</x-slot:header>

            Corpo

            <x-slot:footer>Rodapé</x-slot:footer>
        </x-ui.card>
        BLADE;

    expect((string) $this->blade($blade))->toContain('Título')
        ->toContain('Rodapé')
        ->toContain('border-t border-neutral-100')
        ->and((string) $this->blade('<x-ui.card>Corpo</x-ui.card>'))->not->toContain('border-t border-neutral-100');
});
