<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

/**
 * What the input, the textarea and the select agree on: one look, one size scale, and one
 * answer to what a `type` and an options list may be.
 */
final class FormControl
{
    public const string BASE = 'w-full rounded-control border bg-white text-neutral-900 placeholder:text-neutral-400 transition-[border-color,box-shadow] duration-(--duration-fast) ease-(--ease-fluid) disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-neutral-50';

    public const string VALID = 'border-neutral-300 hover:border-neutral-400';

    /** The red border is doubled by the message under the field: colour is never the only signal. */
    public const string INVALID = 'border-red-500 hover:border-red-600';

    /** @var array<string, string> */
    public const array SIZES = [
        'sm'   => 'h-control-sm px-3 text-sm',
        'base' => 'h-control px-3.5 text-sm',
        'lg'   => 'h-control-lg px-4 text-base',
    ];

    /** @var array<string, string> */
    public const array TEXT_SIZES = [
        'sm'   => 'px-3 text-sm',
        'base' => 'px-3.5 text-sm',
        'lg'   => 'px-4 text-base',
    ];

    /** A checkbox and a radio differ by their radius alone; `accent-*` paints the native control. */
    public const string CHOICE = 'size-4 shrink-0 cursor-pointer border-neutral-300 accent-primary disabled:cursor-not-allowed disabled:opacity-50';

    public const string CHOICE_LABEL = 'flex cursor-pointer items-center gap-2';

    /**
     * The types a text field may carry. Anything else is a typo or an attempt to turn the
     * field into something it is not — a submit button, a file picker — so it reads as text.
     */
    public const array TYPES = [
        'text', 'email', 'password', 'tel', 'url', 'search', 'number',
        'date', 'time', 'datetime-local', 'month', 'week', 'color',
    ];

    public static function type(?string $type): string
    {
        return in_array($type, self::TYPES, true) ? (string) $type : 'text';
    }

    /**
     * Options as `['br' => 'Brasil']`, as `['Brasil', 'Chile']`, or as rows carrying their own
     * `value` and `label`. All three end up in the same shape.
     *
     * @return list<array{value: string, label: string}>
     */
    public static function options(mixed $options): array
    {
        $rows = [];

        foreach ((array) $options as $key => $option) {
            if (is_array($option)) {
                $value = $option['value'] ?? $option['id'] ?? $key;
                $label = $option['label'] ?? $option['name'] ?? $value;
            } else {
                $value = is_string($key) ? $key : $option;
                $label = $option;
            }

            if (is_scalar($value) && is_scalar($label)) {
                $rows[] = ['value' => (string) $value, 'label' => (string) $label];
            }
        }

        return $rows;
    }
}
