<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class ClassList
{
    /**
     * The first segment after the utility name that makes it something other than a colour:
     * `text-lg` is a size, `bg-cover` a size, `border-2` a width.
     */
    private const array NOT_A_COLOR = [
        'text' => [
            'xs', 'sm', 'base', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', '8xl', '9xl',
            'left', 'center', 'right', 'justify', 'start', 'end',
            'wrap', 'nowrap', 'balance', 'pretty', 'ellipsis', 'clip', 'shadow',
        ],
        'bg' => [
            'fixed', 'local', 'scroll', 'clip', 'origin', 'repeat', 'no', 'auto', 'cover', 'contain',
            'top', 'bottom', 'left', 'right', 'center', 'none', 'blend', 'linear', 'radial', 'conic', 'gradient',
            'position', 'size',
        ],
        'border' => [
            '0', '2', '4', '8', 'x', 'y', 't', 'r', 'b', 'l', 's', 'e',
            'solid', 'dashed', 'dotted', 'double', 'hidden', 'none', 'collapse', 'separate', 'spacing',
        ],
    ];

    /**
     * Whether `$classes` already sets the colour `$utility` would set. A component checks this
     * before adding its default colour, because two colour utilities on one element are settled
     * by their order in the stylesheet — alphabetical — and not by the order they were written:
     * `text-blue-700` sorts before `text-neutral-700` and would silently lose to it.
     *
     * Only unprefixed utilities count. `hover:text-red-700` changes the hover, and the resting
     * colour is still the component's to give.
     */
    public static function setsColor(string $classes, string $utility, ?string $variant = null): bool
    {
        $variantPrefix = $variant === null ? '' : $variant . ':';

        foreach (preg_split('/\s+/', trim($classes)) ?: [] as $class) {
            if ($variantPrefix !== '') {
                if (! str_starts_with($class, $variantPrefix)) {
                    continue;
                }

                $class = substr($class, strlen($variantPrefix));
            }

            $class = ltrim($class, '!');

            if (str_contains($class, ':') || ! str_starts_with($class, $utility . '-')) {
                continue;
            }

            if (self::isColor(substr($class, strlen($utility) + 1), $utility)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The component's default, unless the caller already chose a colour for the same utility.
     */
    public static function colorUnlessSet(string $incoming, string $utility, string $default, ?string $variant = null): string
    {
        return self::setsColor($incoming, $utility, $variant) ? '' : $default;
    }

    private static function isColor(string $value, string $utility): bool
    {
        if ($value === '') {
            return false;
        }

        if (str_starts_with($value, '[') || str_starts_with($value, '(')) {
            return ! preg_match('/^[\[(](length:|url\(|image:|\d|calc\()/i', $value);
        }

        $segment = explode('-', $value)[0];

        return ! in_array($segment, self::NOT_A_COLOR[$utility] ?? [], true);
    }
}
