@props([
    'paginator',
    'size'    => null,
    'simple'  => false,
    'summary' => null,
    'rounded' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Pagination;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('pagination');

    $size ??= $ui->default('size', 'base');

    $rounded ??= $ui->default('rounded', 'base');

    $summary ??= $ui->default('summary', true);

    $knowsTotal = $paginator instanceof LengthAwarePaginator;

    /** Without a total there is no page count, so the numbers collapse to previous and next. */
    $numbered = $knowsTotal && ! $simple;

    /** `firstItem()` and not `total()`: a page past the end knows the total and has nothing on it. */
    $showSummary = $summary && $knowsTotal && $paginator->firstItem() !== null;

    $sizes = $ui->sizes([
        'sm'   => 'h-control-xs min-w-control-xs px-1.5 text-xs',
        'base' => 'h-control-sm min-w-control-sm px-2 text-sm',
        'lg'   => 'h-control min-w-control px-2.5 text-base',
    ]);

    $iconSizes = [
        'sm'   => 'size-3.5',
        'base' => 'size-4',
        'lg'   => 'size-5',
    ];

    $radii = [
        'none' => 'rounded-none',
        'sm'   => 'rounded-sm',
        'md'   => 'rounded-md',
        'base' => 'rounded-control',
        'lg'   => 'rounded-lg',
        'full' => 'rounded-full',
    ];

    $roundedClass = $radii[$rounded] ?? (str_starts_with((string) $rounded, 'rounded') ? $rounded : $radii['base']);

    $iconClass = $iconSizes[$size] ?? $iconSizes['base'];

    /** One box for every cell — number, gap and arrow alike — so the row reads as one control. */
    $cell = implode(' ', [
        'inline-flex items-center justify-center font-control whitespace-nowrap',
        'transition-[background-color,color] duration-(--duration-fast) ease-(--ease-fluid)',
        $sizes[$size] ?? $sizes['base'],
        $roundedClass,
    ]);

    $pageClass = $ui->classes('page', $cell . ' text-neutral-700 hover:bg-neutral-100');

    $currentClass = $ui->classes('current', $cell . ' bg-neutral-900 text-white');

    $gapClass = $ui->classes('gap', $cell . ' text-neutral-400');

    $arrowClass = $ui->classes('arrow', $cell . ' text-neutral-700 hover:bg-neutral-100');

    /** A dead arrow keeps its box so the row does not shift width between the first page and the rest. */
    $disabledClass = $ui->classes('disabled', $cell . ' text-neutral-300');

    $previousUrl = SafeUrl::href($paginator->previousPageUrl());

    $nextUrl = SafeUrl::href($paginator->nextPageUrl());
@endphp

@if ($paginator->hasPages())
    <nav
        class="{{ ClassList::merge($ui->classes('base', 'flex flex-wrap items-center justify-between gap-4'), (string) $attributes->get('class')) }}"
        {{ $attributes->except('class')->merge(['aria-label' => 'Paginação']) }}
    >
        @if ($showSummary)
            <p class="{{ $ui->classes('summary', 'text-sm text-neutral-600') }}">
                Mostrando
                <span class="font-control text-neutral-900">{{ $paginator->firstItem() }}</span>–<span
                    class="font-control text-neutral-900"
                    >{{ $paginator->lastItem() }}</span>
                de <span class="font-control text-neutral-900">{{ $paginator->total() }}</span>
            </p>
        @endif

        <ul class="{{ $ui->classes('list', 'flex flex-wrap items-center gap-1') }}">
            <li>
                @if (filled($previousUrl))
                    <a href="{{ $previousUrl }}" rel="prev" aria-label="Página anterior" class="{{ $arrowClass }}">
                        {{ svg('heroicon-m-chevron-left', $iconClass) }}
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Página anterior" class="{{ $disabledClass }}">
                        {{ svg('heroicon-m-chevron-left', $iconClass) }}
                    </span>
                @endif
            </li>

            @if ($numbered)
                @foreach (Pagination::pages($paginator) as $page)
                    <li>
                        @if ($page['gap'])
                            <span aria-hidden="true" class="{{ $gapClass }}">{{ $page['label'] }}</span>
                        @elseif ($page['current'])
                            <span aria-current="page" class="{{ $currentClass }}">{{ $page['label'] }}</span>
                        @else
                            <a
                                href="{{ SafeUrl::href($page['url']) }}"
                                aria-label="Ir para a página {{ $page['label'] }}"
                                class="{{ $pageClass }}"
                            >
                                {{ $page['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            @endif

            <li>
                @if (filled($nextUrl))
                    <a href="{{ $nextUrl }}" rel="next" aria-label="Próxima página" class="{{ $arrowClass }}">
                        {{ svg('heroicon-m-chevron-right', $iconClass) }}
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Próxima página" class="{{ $disabledClass }}">
                        {{ svg('heroicon-m-chevron-right', $iconClass) }}
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
