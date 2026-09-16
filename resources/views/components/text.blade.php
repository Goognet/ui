@props([
    'size'    => 'base',
    'variant' => 'default',
    'inline'  => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    $tag = $inline ? 'span' : 'p';

    $sizes = [
        'sm'   => 'text-sm',
        'base' => 'text-base',
        'lg'   => 'text-lg',
        'xl'   => 'text-xl',
    ];

    /** `subtle` is neutral-600, not lighter: neutral-400 measures 2.6:1 on white and fails body copy. */
    $colors = [
        'default' => 'text-neutral-700',
        'strong'  => 'text-neutral-950',
        'subtle'  => 'text-neutral-600',
    ];

    $classes = [
        $sizes[$size] ?? $sizes['base'],
        ClassList::colorUnlessSet((string) $attributes->get('class'), 'text', $colors[$variant] ?? $colors['default']),
        'font-medium'                 => $variant === 'strong',
        'leading-relaxed text-pretty' => ! $inline,
    ];
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>{{ $slot }}</{{ $tag }}>
