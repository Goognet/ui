<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Catalogue
{
    /**
     * The catalogue's content lives beside the views rather than in `config/`: merged into the
     * config it would be loaded on every request of every site using the package, and cached
     * with it, for a page that only exists outside production.
     *
     * @return list<array<string, mixed>>
     */
    public static function entries(): array
    {
        /** @var list<array<string, mixed>> $entries */
        $entries = require dirname(__DIR__, 2) . '/resources/docs/components.php';

        return $entries;
    }

    /** The tag a reader should type for a component, under whatever prefix the site configured. */
    public static function tag(string $component): string
    {
        $prefix = (string) config('goognet-ui.prefix');

        return $prefix === '' ? 'goognet-ui::' . $component : $prefix . $component;
    }

    /**
     * The examples are written with the default `ui.` prefix. Rewritten to the configured one, so
     * a site that changed it sees code it can copy and previews that still render.
     */
    public static function code(string $code): string
    {
        return (string) preg_replace_callback(
            '#<(/?)x-ui\.([a-z0-9-]+)#',
            fn (array $match): string => '<' . $match[1] . 'x-' . self::tag($match[2]),
            $code,
        );
    }

    /** The agency mark, a file shipped with the package and checked for active content in its tests. */
    public static function logo(): string
    {
        return (string) file_get_contents(dirname(__DIR__, 2) . '/resources/docs/goognet.svg');
    }

    /**
     * Off unless switched on. The documentation lives on GitHub Pages; a site only opens the local
     * copy on purpose, to try a change before it is published.
     */
    public static function enabled(): bool
    {
        return (bool) config('goognet-ui.catalogue.enabled');
    }
}
