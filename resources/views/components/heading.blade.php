@props([
    'size'  => 'base',
    'level' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** Without a level it is a `<div>`: a decorative title stays out of the document outline. */
    $tag = filled($level) && in_array((int) $level, [1, 2, 3, 4, 5, 6], true) ? 'h' . (int) $level : 'div';

    $sizes = [
        'base' => 'text-base font-semibold',
        'lg'   => 'text-lg font-semibold',
        'xl'   => 'text-2xl font-semibold tracking-tight text-balance',
        '2xl'  => 'text-4xl font-semibold tracking-tight text-balance sm:text-5xl',
    ];

    $classes = [
        ClassList::colorUnlessSet((string) $attributes->get('class'), 'text', 'text-neutral-950'),
        $sizes[$size] ?? $sizes['base'],
    ];
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>{{ $slot }}</{{ $tag }}>
