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

    /**
     * The component's classes with the caller's on top, where a caller's utility replaces the
     * component's utility for the same property instead of joining it.
     *
     * Two utilities for one property are settled by their order in the generated stylesheet, not
     * by the order they were written, so `rounded-control rounded-full` is a coin toss. Conflicts
     * are matched per variant: `hover:bg-red-500` replaces the component's hover background and
     * leaves its resting one alone. A class this does not recognise is never removed.
     */
    public static function merge(string ...$layers): string
    {
        $result = [];

        foreach ($layers as $layer) {
            $incoming = array_values(array_filter(preg_split('/\s+/', trim($layer)) ?: []));

            $replaced = [];

            foreach ($incoming as $class) {
                foreach (self::groupsOf($class) as $key) {
                    $replaced[$key] = true;
                }
            }

            $result = array_values(array_filter(
                $result,
                fn (string $class): bool => array_intersect_key(array_flip(self::conflictsOf($class)), $replaced) === [],
            ));

            foreach ($incoming as $class) {
                if (! in_array($class, $result, true)) {
                    $result[] = $class;
                }
            }
        }

        return implode(' ', $result);
    }

    /**
     * The groups a class sets, as `variant|group` keys. `size-*` sets width and height as well as
     * size, so a caller's `size-12` clears a component's `h-10`.
     *
     * @return list<string>
     */
    private static function groupsOf(string $class): array
    {
        [$variant, $utility] = self::split($class);

        $group = self::group($utility);

        if ($group === null) {
            return [];
        }

        $groups = [$group];

        if ($group === 'size') {
            $groups = ['size', 'h', 'w'];
        } elseif ($group === 'p') {
            $groups = ['p', 'px', 'py', 'pt', 'pr', 'pb', 'pl', 'ps', 'pe'];
        } elseif ($group === 'm') {
            $groups = ['m', 'mx', 'my', 'mt', 'mr', 'mb', 'ml', 'ms', 'me'];
        } elseif ($group === 'gap') {
            $groups = ['gap', 'gap-x', 'gap-y'];
        }

        return array_map(fn (string $name): string => $variant . '|' . $name, $groups);
    }

    /**
     * The keys under which an existing class would be replaced.
     *
     * @return list<string>
     */
    private static function conflictsOf(string $class): array
    {
        [$variant, $utility] = self::split($class);

        $group = self::group($utility);

        return $group === null ? [] : [$variant . '|' . $group];
    }

    /**
     * `md:hover:!-translate-y-px` → [`md:hover:`, `translate-y`]. The variant is everything up to
     * the last colon outside brackets, so `data-[state=open]:` stays one variant.
     *
     * @return array{0: string, 1: string}
     */
    private static function split(string $class): array
    {
        $depth = 0;
        $cut   = -1;

        foreach (str_split($class) as $index => $char) {
            $depth += match ($char) {
                '[', '(' => 1,
                ']', ')' => -1,
                default  => 0,
            };

            if ($char === ':' && $depth === 0) {
                $cut = $index;
            }
        }

        $variant = $cut === -1 ? '' : substr($class, 0, $cut + 1);

        $utility = ltrim(substr($class, $cut + 1), '!-');

        return [$variant, $utility];
    }

    private static function group(string $utility): ?string
    {
        if (preg_match('/^(block|inline|inline-block|inline-flex|flex|inline-grid|grid|hidden|contents|table)$/', $utility) === 1) {
            return 'display';
        }

        if (preg_match('/^(static|fixed|absolute|relative|sticky)$/', $utility) === 1) {
            return 'position';
        }

        if (preg_match('/^(uppercase|lowercase|capitalize|normal-case)$/', $utility) === 1) {
            return 'text-transform';
        }

        if (preg_match('/^(underline|overline|line-through|no-underline)$/', $utility) === 1) {
            return 'text-decoration-line';
        }

        if (preg_match('/^rounded(-(none|xs|sm|md|lg|xl|2xl|3xl|4xl|full|control(-[a-z0-9]+)?|\[.+\]|\(.+\)))?$/', $utility) === 1) {
            return 'rounded';
        }

        if (preg_match('/^(size|h|w|min-h|min-w|max-h|max-w|gap-x|gap-y|gap|px|py|pt|pr|pb|pl|ps|pe|p|mx|my|mt|mr|mb|ml|ms|me|m|opacity|leading|tracking|duration|ease|cursor|whitespace|justify|items|self|z|order|inset|top|right|bottom|left)-.+$/', $utility, $match) === 1) {
            return $match[1];
        }

        if (preg_match('/^font-(thin|extralight|light|normal|medium|semibold|bold|extrabold|black|control|\[\d.*\]|\(weight:.+\))$/', $utility) === 1) {
            return 'font-weight';
        }

        if (preg_match('/^shadow(-.+)?$/', $utility) === 1) {
            return 'shadow';
        }

        if (preg_match('/^transition(-.+)?$/', $utility) === 1) {
            return 'transition';
        }

        if (preg_match('/^border(-[0-9]+|-\[\d.*\])?$/', $utility) === 1) {
            return 'border-width';
        }

        foreach (['text', 'bg', 'border'] as $prefix) {
            if (str_starts_with($utility, $prefix . '-')) {
                $value = substr($utility, strlen($prefix) + 1);

                if (self::isColor($value, $prefix)) {
                    return $prefix . '-color';
                }

                if ($prefix === 'text' && preg_match('/^(xs|sm|base|lg|xl|[2-9]xl|control(-[a-z0-9]+)?|\[\d.*\]|\(length:.+\))$/', $value) === 1) {
                    return 'text-size';
                }

                if ($prefix === 'text' && preg_match('/^(left|center|right|justify|start|end)$/', $value) === 1) {
                    return 'text-align';
                }
            }
        }

        return null;
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
