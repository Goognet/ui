<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Support\Facades\Route;
use InvalidArgumentException;

final class Navigation
{
    /**
     * The URL of a navigation item: a route name when one is given, the `url` otherwise.
     *
     * A route name survives a changed path, and it cannot be resolved in a config file, which is
     * read before the router exists. A name that is not registered throws: a menu pointing at
     * nothing is a mistake to see while building the page, not a dead link to find later.
     *
     * @param  array<string, mixed>  $item
     */
    public static function resolve(array $item): ?string
    {
        if (blank($item['route'] ?? null)) {
            return self::anchored(is_string($item['url'] ?? null) ? $item['url'] : null);
        }

        $route = (array) $item['route'];

        $name = (string) array_shift($route);

        if (! Route::has($name)) {
            throw new InvalidArgumentException('O item de navegação aponta para a rota "' . $name . '", que não existe. Item: "' . ($item['label'] ?? '?') . '".');
        }

        return route($name, $route[0] ?? []);
    }

    /**
     * The privacy policy, when the configured route exists. A site without the page yet renders
     * no link to it instead of failing.
     */
    public static function privacyUrl(): ?string
    {
        $name = config('goognet-ui.legal.privacy_route');

        return is_string($name) && $name !== '' && Route::has($name) ? route($name) : null;
    }

    /**
     * An anchor names a section of the home page. Rendered anywhere else it would point at a
     * section that is not there — so off the home page it is prefixed with the home address.
     */
    public static function anchored(?string $url): ?string
    {
        if (blank($url) || ! str_starts_with($url, '#')) {
            return $url;
        }

        $home = rtrim(url('/'), '/');

        return rtrim(request()->url(), '/') === $home ? $url : $home . '/' . $url;
    }
}
