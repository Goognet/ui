<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Table;

it('draws the headers as column headers', function (): void {
    $html = (string) $this->blade('<x-ui.table :headers="[\'Plano\', \'Preço\']" :rows="[[\'Lite\', \'R$ 90\']]" />');

    expect($html)->toContain('<th')
        ->toContain('scope="col"')
        ->toContain('Plano')
        ->toContain('R$ 90');
});

it('scrolls on its own instead of pushing the page sideways', function (): void {
    expect((string) $this->blade('<x-ui.table :headers="[\'a\']" :rows="[[\'b\']]" />'))->toContain('overflow-x-auto');
});

it('reads a row by column key and by position alike', function (mixed $rows): void {
    $html = (string) $this->blade('<x-ui.table :headers="$headers" :rows="$rows" />', [
        'headers' => [['key' => 'plano', 'label' => 'Plano'], ['key' => 'preco', 'label' => 'Preço']],
        'rows'    => $rows,
    ]);

    expect($html)->toContain('Lite')->toContain('R$ 90');
})->with([
    'keyed'      => fn (): array => [['plano' => 'Lite', 'preco' => 'R$ 90']],
    'positional' => fn (): array => [['Lite', 'R$ 90']],
]);

it('aligns a column and its cells the same way', function (): void {
    $html = (string) $this->blade('<x-ui.table :headers="$headers" :rows="[[\'Lite\', \'R$ 90\']]" />', [
        'headers' => ['Plano', ['label' => 'Preço', 'align' => 'end']],
    ]);

    expect(substr_count($html, 'text-end'))->toBe(2);
});

it('stripes the rows only when asked', function (): void {
    expect((string) $this->blade('<x-ui.table :headers="[\'a\']" :rows="[[\'b\']]" striped />'))->toContain('even:bg-neutral-50')
        ->and((string) $this->blade('<x-ui.table :headers="[\'a\']" :rows="[[\'b\']]" />'))->not->toContain('even:bg-neutral-50');
});

it('falls back to the slot when no rows are passed', function (): void {
    $blade = <<<'BLADE'
        <x-ui.table :headers="['Plano']">
            <tr><td>Escrito à mão</td></tr>
        </x-ui.table>
        BLADE;

    expect((string) $this->blade($blade))->toContain('Escrito à mão');
});

it('keeps a caption above the table for a screen reader', function (): void {
    expect((string) $this->blade('<x-ui.table caption="Planos" :headers="[\'a\']" :rows="[[\'b\']]" />'))
        ->toContain('<caption')
        ->toContain('Planos');
});

it('drops a value a cell cannot print', function (): void {
    expect(Table::cells([['um', 'dois'], 'texto'], Table::columns(['a', 'b'])))->toBe(['', 'texto']);
});
