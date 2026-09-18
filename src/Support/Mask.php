<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

/**
 * What a field's `mask` means, resolved on the server so the browser receives a name or a
 * pattern and never a guess. The patterns themselves live in the script, because a couple of
 * them are not strings — money counts digits, it does not match a shape.
 */
final class Mask
{
    /** The presets a site names instead of spelling out. */
    public const array PRESETS = ['phone', 'cpf', 'cnpj', 'cpf-cnpj', 'cep', 'date', 'time', 'money', 'percent', 'card'];

    /**
     * A preset is passed by name; anything else is taken as an IMask pattern, where `0` is a
     * digit and `a` a letter. A pattern is filtered down to the characters a mask can carry,
     * so a value out of a database or a query string cannot smuggle markup into the attribute.
     *
     * @return array{preset: string}|array{pattern: string}|null
     */
    public static function resolve(mixed $mask): ?array
    {
        if (blank($mask) || ! is_string($mask)) {
            return null;
        }

        if (in_array($mask, self::PRESETS, true)) {
            return ['preset' => $mask];
        }

        $pattern = (string) preg_replace('/[^0*aA9#\[\]{}()\/.,:\- ]/', '', $mask);

        return $pattern === '' ? null : ['pattern' => $pattern];
    }

    /**
     * The keyboard the field asks a phone for. A mask of digits with a text keyboard is how a
     * form ends up being typed with the wrong half of the keypad on a phone.
     */
    /**
     * @param  array{preset: string}|array{pattern: string}|null  $mask
     */
    public static function inputMode(?array $mask): ?string
    {
        if ($mask === null) {
            return null;
        }

        $preset = $mask['preset'] ?? null;

        if (in_array($preset, ['phone', 'cpf', 'cnpj', 'cpf-cnpj', 'cep', 'card', 'date', 'time'], true)) {
            return 'numeric';
        }

        if (in_array($preset, ['money', 'percent'], true)) {
            return 'decimal';
        }

        /** A written pattern only asks for the numeric keypad when it holds no letters. */
        return isset($mask['pattern']) && preg_match('/[aA*]/', $mask['pattern']) !== 1 ? 'numeric' : null;
    }
}
