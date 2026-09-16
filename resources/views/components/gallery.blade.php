@props([
    'columns'  => ['base' => 2, 'md' => 3],
    'gap'      => 4,
    'lightbox' => false,
    'label'    => null,
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** Written out in full so Tailwind's scanner sees them; an interpolated class never ships. */
    $columnClasses = [
        'base' => [1 => 'grid-cols-1', 2 => 'grid-cols-2', 3 => 'grid-cols-3', 4 => 'grid-cols-4', 5 => 'grid-cols-5', 6 => 'grid-cols-6'],
        'sm'   => [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4', 5 => 'sm:grid-cols-5', 6 => 'sm:grid-cols-6'],
        'md'   => [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', 5 => 'md:grid-cols-5', 6 => 'md:grid-cols-6'],
        'lg'   => [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5', 6 => 'lg:grid-cols-6'],
        'xl'   => [1 => 'xl:grid-cols-1', 2 => 'xl:grid-cols-2', 3 => 'xl:grid-cols-3', 4 => 'xl:grid-cols-4', 5 => 'xl:grid-cols-5', 6 => 'xl:grid-cols-6'],
        '2xl'  => [1 => '2xl:grid-cols-1', 2 => '2xl:grid-cols-2', 3 => '2xl:grid-cols-3', 4 => '2xl:grid-cols-4', 5 => '2xl:grid-cols-5', 6 => '2xl:grid-cols-6'],
    ];

    $gapClasses = [2 => 'gap-2', 3 => 'gap-3', 4 => 'gap-4', 5 => 'gap-5', 6 => 'gap-6', 8 => 'gap-8', 10 => 'gap-10', 12 => 'gap-12'];

    $map = is_array($columns) ? $columns : ['base' => $columns];

    $grid = collect($columnClasses)
        ->filter(fn (array $_, string $at): bool => isset($map[$at]))
        ->map(fn (array $available, string $at): ?string => $available[(int) $map[$at]] ?? null)
        ->filter()
        ->values()
        ->all();

    if (blank($grid)) {
        $grid = [$columnClasses['base'][2]];
    }
@endphp

<ul
    {{ $attributes->class(['grid', ...$grid, $gapClasses[(int) $gap] ?? $gapClasses[4]]) }}
    @if (filled($label)) aria-label="{{ $label }}" @endif
>
    {{ $slot }}
</ul>
