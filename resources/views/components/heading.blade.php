@props([
    'size'  => null,
    'level' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('heading');

    $attributes = SafeUrl::attributes($attributes);

    $size ??= $ui->default('size', 'base');

    $level ??= $ui->default('level', null);

    /** Without a level it is a `<div>`: a decorative title stays out of the document outline. */
    $tag = filled($level) && in_array((int) $level, [1, 2, 3, 4, 5, 6], true) ? 'h' . (int) $level : 'div';

    $sizes = $ui->sizes([
        'base' => 'text-base font-heading',
        'lg'   => 'text-lg font-heading',
        'xl'   => 'text-2xl font-heading tracking-tight text-balance',
        '2xl'  => 'text-4xl font-heading tracking-tight text-balance sm:text-5xl',
    ]);

    $classes = ClassList::merge(
        $ui->classes('base', 'text-neutral-950 ' . ($sizes[$size] ?? $sizes['base'])),
        (string) $attributes->get('class'),
    );
@endphp

<{{ $tag }} class="{{ $classes }}" {{ $attributes->except('class') }}>{{ $slot }}</{{ $tag }}>
