<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;

/**
 * Flattens Laravel's page window into the single list the view draws.
 *
 * `LengthAwarePaginator::elements()` is protected, and what it returns is a mix of arrays and
 * the literal string `...`. That is why every stock pagination view repeats the same nested
 * loop with an `is_string()` branch inside it. The shape is resolved once here instead.
 */
final class Pagination
{
    /**
     * @param  LengthAwarePaginator<array-key, mixed>  $paginator
     * @return list<array{label: string, url: string|null, current: bool, gap: bool}>
     */
    public static function pages(LengthAwarePaginator $paginator): array
    {
        $window = UrlWindow::make($paginator);

        $pages = [];

        /** `first`, then `slider`, then `last` — a gap stands wherever a later segment starts. */
        foreach ([$window['first'], $window['slider'], $window['last']] as $index => $segment) {
            if (! is_array($segment)) {
                continue;
            }

            if ($index > 0 && $pages !== []) {
                $pages[] = ['label' => '…', 'url' => null, 'current' => false, 'gap' => true];
            }

            foreach ($segment as $page => $url) {
                $pages[] = [
                    'label'   => (string) $page,
                    'url'     => $url,
                    'current' => $page === $paginator->currentPage(),
                    'gap'     => false,
                ];
            }
        }

        return $pages;
    }
}
