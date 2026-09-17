@props([
    'size'    => null,
    'variant' => null,
    'inline'  => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('text');

    $attributes = SafeUrl::attributes($attributes);

    $size ??= $ui->default('size', 'base');

    $variant ??= $ui->default('variant', 'default');

    $tag = $inline ? 'span' : 'p';

    $sizes = $ui->sizes([
        'sm'   => 'text-sm',
        'base' => 'text-base',
        'lg'   => 'text-lg',
        'xl'   => 'text-xl',
    ]);

    /** `subtle` is neutral-600, not lighter: neutral-400 measures 2.6:1 on white and fails body copy. */
    $variants = $ui->variants([
        'default' => 'text-neutral-700',
        'strong'  => 'font-control text-neutral-950',
        'subtle'  => 'text-neutral-600',
    ]);

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', array_filter([
            $sizes[$size] ?? $sizes['base'],
            $variants[$variant] ?? $variants['default'],
            $inline ? null : 'leading-relaxed text-pretty',
        ]))),
        (string) $attributes->get('class'),
    );
@endphp

<{{ $tag }} class="{{ $classes }}" {{ $attributes->except('class') }}>{{ $slot }}</{{ $tag }}>
