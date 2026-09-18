<?php

declare(strict_types = 1);

it('says nothing when there is nothing to say', function (): void {
    expect(trim((string) $this->blade('<x-ui.toast />')))->toBeEmpty();
});

it('carries the message passed at the call site', function (): void {
    $html = (string) $this->blade('<x-ui.toast message="Mensagem enviada." type="success" />');

    preg_match('/data-toast="([^"]+)"/', $html, $matches);

    expect(json_decode(html_entity_decode($matches[1]), true))->toMatchArray([
        'message'  => 'Mensagem enviada.',
        'icon'     => 'success',
        'position' => 'top-end',
    ]);
});

it('shows what the last request flashed', function (string $key, string $icon): void {
    session()->flash($key, 'Deu certo.');

    $html = (string) $this->blade('<x-ui.toast />');

    preg_match('/data-toast="([^"]+)"/', $html, $matches);

    expect(json_decode(html_entity_decode($matches[1]), true))->toMatchArray([
        'message' => 'Deu certo.',
        'icon'    => $icon,
    ]);
})->with([
    ['success', 'success'],
    ['status', 'success'],
    ['error', 'error'],
    ['warning', 'warning'],
    ['info', 'info'],
]);

it('reads a flash that carries its own title and icon', function (): void {
    session()->flash('toast', ['message' => 'Arquivo enviado.', 'title' => 'Pronto', 'type' => 'success']);

    $html = (string) $this->blade('<x-ui.toast />');

    preg_match('/data-toast="([^"]+)"/', $html, $matches);

    expect(json_decode(html_entity_decode($matches[1]), true))->toMatchArray([
        'message' => 'Arquivo enviado.',
        'title'   => 'Pronto',
        'icon'    => 'success',
    ]);
});

it('refuses an icon and a position it does not know', function (): void {
    $html = (string) $this->blade('<x-ui.toast message="x" type="explosão" position="no-meio-da-tela" />');

    $config = json_decode(html_entity_decode(preg_replace('/.*data-toast="([^"]+)".*/s', '$1', $html)), true);

    expect($config)->not->toHaveKey('icon')
        ->and($config['position'])->toBe('top-end');
});

it('keeps the duration between a second and a minute', function (int $passed, int $kept): void {
    $html = (string) $this->blade('<x-ui.toast message="x" :duration="' . $passed . '" />');

    $config = json_decode(html_entity_decode(preg_replace('/.*data-toast="([^"]+)".*/s', '$1', $html)), true);

    expect($config['duration'])->toBe($kept);
})->with([
    [0, 1000],
    [200, 1000],
    [4000, 4000],
    [600000, 60000],
]);

it('escapes a message into the attribute', function (): void {
    session()->flash('error', '<script>alert(1)</script>');

    expect((string) $this->blade('<x-ui.toast />'))->not->toContain('<script>');
});

it('reads only the key it was given', function (): void {
    session()->flash('success', 'Do success');
    session()->flash('meu-aviso', 'Do meu aviso');

    $html = (string) $this->blade('<x-ui.toast session="meu-aviso" />');

    expect($html)->toContain('Do meu aviso')
        ->not->toContain('Do success');
});
