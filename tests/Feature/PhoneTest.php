<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Phone;

it('reduces a written number to digits', function (?string $written, string $expected): void {
    expect(Phone::digits($written))->toBe($expected);
})->with([
    'formato da agência' => ['(011) 91234-5678', '011912345678'],
    'sem o zero'         => ['(11) 91234-5678', '11912345678'],
    'fixo'               => ['(11) 3456-7890', '1134567890'],
    'espaços e pontos'   => ['11 9 1234.5678', '11912345678'],
    'já limpo'           => ['011912345678', '011912345678'],
    /** It strips, it does not interpret: a written country code survives, none is invented. */
    'com código de país' => ['+55 (11) 91234-5678', '5511912345678'],
    'vazio'              => ['', ''],
    'nulo'               => [null, ''],
    'só pontuação'       => ['(  ) -', ''],
]);

it('leaves a tel: href with nothing but digits after the colon', function (): void {
    /**
     * The reason the helper exists. Four call sites carried their own `preg_replace`, and a
     * `tel:` that keeps parentheses or spaces is a link some dialers refuse to open.
     */
    $href = 'tel:' . Phone::digits('(011) 91234-5678');

    expect($href)
        ->toBe('tel:011912345678')
        ->and($href)->toMatch('/^tel:\d+$/');
});
