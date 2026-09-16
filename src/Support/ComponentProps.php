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

    private static function directory(): string
    {
        return dirname(__DIR__, 2) . '/resources/views/components';
    }
}
