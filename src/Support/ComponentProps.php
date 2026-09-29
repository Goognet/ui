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
