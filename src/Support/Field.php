<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

/**
 * The parts a labelled form control needs before it can be drawn: a stable id to tie the
 * label to, the message the validator left for it, and the ids a screen reader has to be
 * pointed at. Shared so the input, the textarea and the select answer the same way.
 */
final class Field
{
    /**
     * The id used by `for`, `aria-describedby` and the error message. A name is enough for
     * the common case; `items[0][qty]` is reduced to something an id attribute accepts.
     */
    public static function id(?string $id, ?string $name): string
    {
        if (filled($id)) {
            return self::slug((string) $id);
        }

        $slug = filled($name) ? self::slug((string) $name) : '';

        return $slug === '' ? uniqid('field-') : $slug;
    }

    /**
     * The message the last validation run left for this field. A message passed at the call
     * site wins, so a form that validates by hand still says what it needs to.
     */
    public static function error(?string $error, ?string $name): ?string
    {
        if (filled($error)) {
            return $error;
        }

        if (blank($name)) {
            return null;
        }

        $bag = view()->shared('errors');

        if (! $bag instanceof ViewErrorBag && ! $bag instanceof MessageBag) {
            return null;
        }

        return $bag->first(self::key((string) $name)) ?: null;
    }

    /**
     * What the visitor typed the last time the form was sent back. Read from the session
     * rather than through `old()`, which answers null when the view is rendered outside a
     * request that carries one.
     */
    public static function old(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        $key = self::key((string) $name);

        $value = request()->hasSession() ? request()->old($key) : session()->getOldInput($key);

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param  array<string, bool>  $ids  id => whether it is rendered
     */
    public static function describedBy(array $ids): ?string
    {
        $described = implode(' ', array_keys(array_filter($ids)));

        return $described === '' ? null : $described;
    }

    /** `items[0][qty]` is stored as `items.0.qty`, by the validator and by the old input alike. */
    private static function key(string $name): string
    {
        return str_replace(['[', ']'], ['.', ''], $name);
    }

    private static function slug(string $value): string
    {
        return trim((string) preg_replace('/[^A-Za-z0-9_-]+/', '-', $value), '-');
    }
}
