<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Pagination;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

function lengthAware(int $total, int $perPage = 10, int $page = 1): LengthAwarePaginator
{
    return new LengthAwarePaginator(array_fill(0, min($perPage, $total), 'item'), $total, $perPage, $page, ['path' => '/artigos']);
}

it('stays out of the page when there is only one page', function (): void {
    expect(trim((string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(4)])))->toBeEmpty();
});

it('marks the page being read and links the others', function (): void {
    $html = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(50, page: 3)]);

    expect($html)->toContain('aria-current="page"')
        ->toContain('href="/artigos?page=2"')
        ->toContain('href="/artigos?page=4"')
        ->toContain('aria-label="Ir para a página 4"')
        /** The current page is text, not a link back to where the reader already is. */
        ->not->toContain('href="/artigos?page=3"');
});

it('names the navigation and both arrows for a screen reader', function (): void {
    $html = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(50, page: 3)]);

    expect($html)->toContain('aria-label="Paginação"')
        ->toContain('aria-label="Página anterior"')
        ->toContain('aria-label="Próxima página"')
        ->toContain('rel="prev"')
        ->toContain('rel="next"');
});

it('keeps a dead arrow as a box instead of dropping it', function (): void {
    $first = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(50)]);
    $last  = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(50, page: 5)]);

    expect($first)->toContain('aria-disabled="true"')->not->toContain('rel="prev"')
        ->and($last)->toContain('aria-disabled="true"')->not->toContain('rel="next"');
});

it('says how much of the total is on screen, and hides it on request', function (): void {
    $paginator = lengthAware(300, page: 7);

    expect((string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator]))
        ->toContain('Mostrando')
        ->toContain('61')
        ->toContain('70')
        ->toContain('300')
        ->and((string) $this->blade('<x-ui.pagination :summary="false" :paginator="$paginator" />', ['paginator' => $paginator]))->not->toContain('Mostrando');
});

it('drops the numbers for a paginator that cannot know the total', function (mixed $paginator): void {
    $html = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator]);

    expect($html)->toContain('rel="next"')
        ->not->toContain('aria-label="Ir para a página 2"')
        ->not->toContain('Mostrando');
})->with([
    /** One item past the page is what tells either paginator there is a next page at all. */
    'simple' => fn (): Paginator => new Paginator(array_fill(0, 11, 'item'), 10, 1, ['path' => '/artigos']),
    'cursor' => fn (): CursorPaginator => new CursorPaginator(collect(range(1, 11))->map(fn (int $id): object => (object) ['id' => $id]), 10, null, ['path' => '/artigos', 'parameters' => ['id']]),
]);

it('forces previous and next on a paginator that does know the total', function (): void {
    $html = (string) $this->blade('<x-ui.pagination simple :paginator="$paginator" />', ['paginator' => lengthAware(300, page: 7)]);

    expect($html)->toContain('rel="next"')
        ->toContain('Mostrando')
        ->not->toContain('aria-label="Ir para a página 8"');
});

it('sizes and rounds every cell the same way', function (): void {
    $html = (string) $this->blade('<x-ui.pagination size="lg" rounded="full" :paginator="$paginator" />', ['paginator' => lengthAware(50, page: 3)]);

    expect($html)->toContain('h-control')->toContain('rounded-full');
});

it('builds the page window with a gap on each side of a long run', function (): void {
    $pages = Pagination::pages(lengthAware(300, page: 15));

    $labels = array_column($pages, 'label');
    $gaps   = array_values(array_filter($pages, fn (array $page): bool => $page['gap']));

    expect($labels[0])->toBe('1')
        ->and(end($labels))->toBe('30')
        ->and($gaps)->toHaveCount(2)
        ->and(collect($pages)->where('current', true)->pluck('label')->all())->toBe(['15']);
});

it('lists every page with no gap while the run is short', function (): void {
    $pages = Pagination::pages(lengthAware(50, page: 2));

    expect(array_column($pages, 'label'))->toBe(['1', '2', '3', '4', '5'])
        ->and(array_filter($pages, fn (array $page): bool => $page['gap']))->toBeEmpty();
});

it('draws the component behind the paginator default view', function (): void {
    Paginator::defaultView('goognet-ui::pagination');

    expect((string) lengthAware(50, page: 3)->links())->toContain('aria-label="Paginação"');
});

it('leaves the summary out of a page that has nothing on it', function (): void {
    /** A total says how many rows exist, not that this page holds any of them. */
    $beyondTheEnd = new LengthAwarePaginator([], 300, 10, 40, ['path' => '/artigos']);

    expect((string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $beyondTheEnd]))
        ->not->toContain('Mostrando');
});

it('shows only the page you are on until there is room for the window', function (): void {
    $html = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(300, page: 7)]);

    $items = [];
    preg_match_all('/<li([^>]*)>\s*<(a|span)[^>]*?(aria-current="page")?[^>]*>\s*([0-9…]+)/u', $html, $items, PREG_SET_ORDER);

    $hidden = collect($items)->filter(fn (array $item): bool => str_contains($item[1], 'hidden sm:block'))->pluck(4);
    $shown  = collect($items)->reject(fn (array $item): bool => str_contains($item[1], 'hidden sm:block'))->pluck(4);

    /** Everything but the current page steps aside on a phone; nothing steps aside from `sm` up. */
    expect($shown->all())->toBe(['7'])
        ->and($hidden)->toContain('1', '6', '8', '30', '…');
});

it('keeps every page in the markup for a crawler, hidden or not', function (): void {
    $html = (string) $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => lengthAware(300, page: 7)]);

    /** `hidden` is a class, not a missing element: the anchor is still there to be followed. */
    expect($html)->toContain('href="/artigos?page=1"')
        ->toContain('href="/artigos?page=30"')
        ->toContain('href="/artigos?page=6"');
});
