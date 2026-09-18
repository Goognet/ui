<?php

declare(strict_types = 1);

it('prints the final number on the server', function (): void {
    $html = (string) $this->blade('<x-ui.counter :value="1250" />');

    expect($html)->toContain('>1.250</span>');
});

it('formats with the separators the site asked for', function (): void {
    $html = (string) $this->blade('<x-ui.counter :value="1234.5" :decimals="1" separator="," decimal="." prefix="R$ " suffix=" mi" />');

    expect($html)->toContain('R$ 1,234.5 mi');
});

it('hands the script what it needs to count', function (): void {
    $html = (string) $this->blade('<x-ui.counter :value="90" :start="10" :duration="3" suffix="%" />');

    preg_match('/data-counter="([^"]+)"/', $html, $matches);

    $config = json_decode(html_entity_decode($matches[1]), true);

    expect($config)->toMatchArray([
        'value'    => 90,
        'start'    => 10,
        'duration' => 3,
        'suffix'   => '%',
    ]);
});

it('caps the decimal places instead of formatting a number nobody can read', function (): void {
    $html = (string) $this->blade('<x-ui.counter :value="1.23456789" :decimals="12" />');

    preg_match('/data-counter="([^"]+)"/', $html, $matches);

    expect(json_decode(html_entity_decode($matches[1]), true)['decimals'])->toBe(4)
        ->and($html)->toContain('1,2346');
});

it('takes a class at the call site over its own size', function (): void {
    expect((string) $this->blade('<x-ui.counter :value="7" size="xl" class="text-base" />'))
        ->toContain('text-base')
        ->not->toContain('text-6xl');
});
