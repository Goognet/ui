<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Mask;

it('passes a preset by name', function (string $preset): void {
    expect(Mask::resolve($preset))->toBe(['preset' => $preset]);
})->with(Mask::PRESETS);

it('takes anything else as a pattern', function (): void {
    expect(Mask::resolve('AAA-0000'))->toBe(['pattern' => 'AAA-0000']);
});

it('answers nothing for a field with no mask', function (mixed $mask): void {
    expect(Mask::resolve($mask))->toBeNull();
})->with([null, '', 0, '<>']);

it('answers nothing for a mask that is not a string', function (): void {
    expect(Mask::resolve(['phone']))->toBeNull();
});

it('strips what a mask cannot carry, so a value cannot reach the attribute as markup', function (): void {
    expect(Mask::resolve('00"><script>alert(1)</script>'))->toBe(['pattern' => '00a()/']);
});

it('asks for the keypad a mask of digits needs', function (string $mask, ?string $mode): void {
    expect(Mask::inputMode(Mask::resolve($mask)))->toBe($mode);
})->with([
    ['phone', 'numeric'],
    ['cpf', 'numeric'],
    ['cep', 'numeric'],
    ['money', 'decimal'],
    ['percent', 'decimal'],
    ['00/00/0000', 'numeric'],
    ['AAA-0000', null],
    ['date', 'numeric'],
]);

it('marks the field for the script and asks for the right keyboard', function (): void {
    $html = (string) $this->blade('<x-ui.input name="tel" mask="phone" />');

    expect($html)->toContain('data-mask=')
        ->toContain('phone')
        ->toContain('inputmode="numeric"');
});

it('leaves a field with no mask alone', function (): void {
    expect((string) $this->blade('<x-ui.input name="nome" />'))
        ->not->toContain('data-mask')
        ->not->toContain('inputmode');
});

it('escapes the mask into the attribute', function (): void {
    $html = (string) $this->blade('<x-ui.input name="x" mask=\'00"><script>\' />');

    expect($html)->not->toContain('<script>');
});
