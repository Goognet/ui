<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Support\Str;

/**
 * Reads the props a component declares, so the catalogue table and the test guarding it work
 * from the component's own source instead of a copy that drifts.
 */
final class ComponentProps
{
    /**
     * @return list<array{name: string, default: string|null, aware: bool}>
     */
    public static function of(string $component): array
    {
        $path = self::directory() . '/' . $component . '.blade.php';

        if (preg_match('/^[a-z0-9-]+$/', $component) !== 1 || ! is_file($path)) {
            return [];
        }

        $source = (string) file_get_contents($path);

        $props = [];

        foreach (['@aware' => true, '@props' => false] as $directive => $isAware) {
            if (preg_match('/' . $directive . '\(\[(.*?)\]\)/s', $source, $block) !== 1) {
                continue;
            }

            foreach (preg_split('/,\s*\n/', trim($block[1])) ?: [] as $line) {
                if (preg_match("/'([^']+)'\s*(?:=>\s*(.+?))?,?\s*$/s", trim($line), $prop) !== 1) {
                    continue;
                }

                $props[] = [
                    'name'    => Str::kebab($prop[1]),
                    'default' => trim($prop[2] ?? '') ?: null,
                    'aware'   => $isAware,
                ];
            }
        }

        return $props;
    }

    /**
     * @return list<string>
     */
    public static function components(): array
    {
        return array_map(
            fn (string $path): string => basename($path, '.blade.php'),
            glob(self::directory() . '/*.blade.php') ?: [],
        );
    }

    /**
     * The names a component accepts for `variant` and `size`, read from the arrays it hands to
     * `$ui->variants()` and `$ui->sizes()`.
     *
     * A prop table saying `variant` accepts a string is not documentation: the reader still has
     * to open the component to learn which strings. An unknown name falls back to the default
     * without a word, so there is nothing on screen to work it out from either.
     *
     * @return array{variants: list<string>, sizes: list<string>}
     */
    public static function options(string $component): array
    {
        $path = self::directory() . '/' . $component . '.blade.php';

        if (preg_match('/^[a-z0-9-]+$/', $component) !== 1 || ! is_file($path)) {
            return ['variants' => [], 'sizes' => []];
        }

        $source = (string) file_get_contents($path);

        return [
            'variants' => self::keysOf($source, 'variants'),
            'sizes'    => self::keysOf($source, 'sizes'),
        ];
    }

    /**
     * The parts of a component that `Ui::component()->part()` can paint, read from the
     * `$ui->classes('name', …)` calls in its source.
     *
     * @return list<string>
     */
    public static function parts(string $component): array
    {
        preg_match_all("/\\\$ui->classes\('([a-z0-9-]+)'/", self::sourceOf($component), $names);

        return array_values(array_unique($names[1]));
    }

    /**
     * The theme tokens a component's classes resolve to, so its page can name what redefining
     * each one moves. Only the utilities the package defines as tokens count: a `p-6` is a
     * Tailwind step and not something a site is meant to retheme.
     *
     * @return list<string>
     */
    public static function tokens(string $component): array
    {
        $source = self::sourceOf($component);

        $utilities = [
            'control'              => '--spacing-control',
            'control-xs'           => '--spacing-control-xs',
            'control-sm'           => '--spacing-control-sm',
            'control-lg'           => '--spacing-control-lg',
            'rounded-control'      => '--radius-control',
            'rounded-surface'      => '--radius-surface',
            'rounded-media'        => '--radius-media',
            'font-control'         => '--font-weight-control',
            'font-heading'         => '--font-weight-heading',
            'shadow-control'       => '--shadow-control',
            'shadow-control-hover' => '--shadow-control-hover',
            'shadow-surface'       => '--shadow-surface',
            'shadow-soft'          => '--shadow-soft',
            'shadow-lifted'        => '--shadow-lifted',
            'container-page'       => '--container-page',
        ];

        $found = [];

        foreach ($utilities as $utility => $token) {
            /**
             * `h-control` and `size-control-sm` both come from the spacing token, so the prefix
             * varies. The lookahead keeps `shadow-control` out of `shadow-control-hover`, which
             * is a token of its own and would otherwise be reported as both.
             */
            $pattern = str_starts_with($utility, 'control')
                ? '/\b(?:h|w|size|min-h)-' . preg_quote($utility, '/') . '(?![\w-])/'
                : '/\b' . preg_quote($utility, '/') . '(?![\w-])/';

            if (preg_match($pattern, $source) === 1) {
                $found[] = $token;
            }
        }

        return array_values(array_unique($found));
    }

    private static function sourceOf(string $component): string
    {
        $path = self::directory() . '/' . $component . '.blade.php';

        return preg_match('/^[a-z0-9-]+$/', $component) === 1 && is_file($path)
            ? (string) file_get_contents($path)
            : '';
    }

    /**
     * @return list<string>
     */
    private static function keysOf(string $source, string $method): array
    {
        if (preg_match('/\$ui->' . $method . '\(\[(.*?)\n    \]\)/s', $source, $block) !== 1) {
            return [];
        }

        preg_match_all("/'([a-z0-9-]+)'\s*=>/", $block[1], $names);

        return array_values(array_unique($names[1]));
    }

    private static function directory(): string
    {
        return dirname(__DIR__, 2) . '/resources/views/components';
    }
}
