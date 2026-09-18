<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

/**
 * Turns what a call site passes into the two shapes the table view draws: a list of columns
 * and, for each row, the cells in the columns' order.
 */
final class Table
{
    private const array ALIGNMENTS = [
        'start'  => 'text-start',
        'center' => 'text-center',
        'end'    => 'text-end',
    ];

    /**
     * Headers as `['Produto', 'Preço']`, as `['produto' => 'Produto']`, or as rows carrying
     * their own `label`, `key` and `align`.
     *
     * @return list<array{key: string|int, label: string, align: string}>
     */
    public static function columns(mixed $headers): array
    {
        $columns = [];

        foreach ((array) $headers as $key => $header) {
            if (is_array($header)) {
                $label = $header['label'] ?? '';
                $align = $header['align'] ?? 'start';
                $key   = $header['key'] ?? $key;
            } else {
                $label = $header;
                $align = 'start';
            }

            if (is_scalar($label)) {
                $columns[] = [
                    'key'   => is_scalar($key) ? $key : 0,
                    'label' => (string) $label,
                    'align' => self::alignmentOf($align),
                ];
            }
        }

        return $columns;
    }

    /**
     * A row is read by column key when it is keyed, and by position when it is a plain list —
     * so a collection out of the database and a hand-written array both draw.
     *
     * @param  list<array{key: string|int, label: string, align: string}>  $columns
     * @return list<string>
     */
    public static function cells(mixed $row, array $columns): array
    {
        $values = is_array($row) ? $row : (array) $row;

        if ($columns === []) {
            return array_values(array_map(self::text(...), $values));
        }

        $cells = [];

        foreach ($columns as $position => $column) {
            $value = $values[$column['key']] ?? $values[$position] ?? null;

            $cells[] = self::text($value);
        }

        return $cells;
    }

    public static function alignment(string $align): string
    {
        return self::ALIGNMENTS[$align] ?? self::ALIGNMENTS['start'];
    }

    private static function alignmentOf(mixed $align): string
    {
        return is_string($align) && array_key_exists($align, self::ALIGNMENTS) ? $align : 'start';
    }

    private static function text(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
