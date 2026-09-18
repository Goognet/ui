@props([
    'columns'  => ['base' => 2, 'md' => 3],
    'gap'      => 4,
    'lightbox' => false,
    'label'    => null,
    'masonry'  => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('gallery');

    /** Written out in full so Tailwind's scanner sees them; an interpolated class never ships. */
    $columnClasses = [
        'base' => [1 => 'grid-cols-1', 2 => 'grid-cols-2', 3 => 'grid-cols-3', 4 => 'grid-cols-4', 5 => 'grid-cols-5', 6 => 'grid-cols-6'],
        'sm'   => [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4', 5 => 'sm:grid-cols-5', 6 => 'sm:grid-cols-6'],
        'md'   => [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', 5 => 'md:grid-cols-5', 6 => 'md:grid-cols-6'],
        'lg'   => [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5', 6 => 'lg:grid-cols-6'],
        'xl'   => [1 => 'xl:grid-cols-1', 2 => 'xl:grid-cols-2', 3 => 'xl:grid-cols-3', 4 => 'xl:grid-cols-4', 5 => 'xl:grid-cols-5', 6 => 'xl:grid-cols-6'],
        '2xl'  => [1 => '2xl:grid-cols-1', 2 => '2xl:grid-cols-2', 3 => '2xl:grid-cols-3', 4 => '2xl:grid-cols-4', 5 => '2xl:grid-cols-5', 6 => '2xl:grid-cols-6'],
    ];

    /**
     * Masonry is CSS columns, not a grid: the rows do not line up, so each picture keeps its
     * own height instead of being cropped to a shared one. The trade is reading order —
     * columns flow top to bottom, so item two sits under item one, not beside it.
     */
    $columnCountClasses = [
        'base' => [1 => 'columns-1', 2 => 'columns-2', 3 => 'columns-3', 4 => 'columns-4', 5 => 'columns-5', 6 => 'columns-6'],
        'sm'   => [1 => 'sm:columns-1', 2 => 'sm:columns-2', 3 => 'sm:columns-3', 4 => 'sm:columns-4', 5 => 'sm:columns-5', 6 => 'sm:columns-6'],
        'md'   => [1 => 'md:columns-1', 2 => 'md:columns-2', 3 => 'md:columns-3', 4 => 'md:columns-4', 5 => 'md:columns-5', 6 => 'md:columns-6'],
        'lg'   => [1 => 'lg:columns-1', 2 => 'lg:columns-2', 3 => 'lg:columns-3', 4 => 'lg:columns-4', 5 => 'lg:columns-5', 6 => 'lg:columns-6'],
        'xl'   => [1 => 'xl:columns-1', 2 => 'xl:columns-2', 3 => 'xl:columns-3', 4 => 'xl:columns-4', 5 => 'xl:columns-5', 6 => 'xl:columns-6'],
        '2xl'  => [1 => '2xl:columns-1', 2 => '2xl:columns-2', 3 => '2xl:columns-3', 4 => '2xl:columns-4', 5 => '2xl:columns-5', 6 => '2xl:columns-6'],
    ];

    $gapClasses = [2 => 'gap-2', 3 => 'gap-3', 4 => 'gap-4', 5 => 'gap-5', 6 => 'gap-6', 8 => 'gap-8', 10 => 'gap-10', 12 => 'gap-12'];

    $map = is_array($columns) ? $columns : ['base' => $columns];

    $grid = collect($masonry ? $columnCountClasses : $columnClasses)
        ->filter(fn (array $_, string $at): bool => isset($map[$at]))
        ->map(fn (array $available, string $at): ?string => $available[(int) $map[$at]] ?? null)
        ->filter()
        ->values()
        ->all();

    if (blank($grid)) {
        $grid = [$masonry ? $columnCountClasses['base'][2] : $columnClasses['base'][2]];
    }
@endphp

<ul
    class="{{ ClassList::merge($ui->classes('base', implode(' ', array_filter([$masonry ? null : 'grid', ...$grid, $gapClasses[(int) $gap] ?? $gapClasses[4]]))), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
    @if (filled($label)) aria-label="{{ $label }}" @endif
>
    {{ $slot }}
</ul>
