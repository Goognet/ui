@props([
    'href'     => null,
    'external' => false,
    'variant'  => null,
    'padding'  => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('card');

    $variant ??= $ui->default('variant', 'default');

    $padding ??= $ui->default('padding', 'base');

    $safeHref = SafeUrl::href($href);

    $tag = filled($safeHref) ? 'a' : 'div';

    $variants = $ui->variants([
        'default'  => 'border border-neutral-200 bg-white',
        'elevated' => 'bg-white shadow-surface',
        'filled'   => 'bg-neutral-50',
        'ghost'    => 'bg-transparent',
    ]);

    $paddings = [
        'none' => '',
        'sm'   => 'p-4',
        'base' => 'p-5',
        'lg'   => 'p-8',
    ];

    /** Only a card that leads somewhere lifts: movement promises a click. */
    $interactive = $tag === 'a'
        ? 'transition-[translate,box-shadow] duration-(--duration-base) ease-(--ease-fluid) hover:-translate-y-0.5 hover:shadow-control-hover'
        : null;

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', array_filter([
            'rounded-surface',
            $variants[$variant] ?? $variants['default'],
            $paddings[$padding] ?? $paddings['base'],
            $interactive,
        ]))),
        (string) $attributes->get('class'),
    );

    $opensInNewTab = filled($safeHref) && ($external || $attributes->get('target') === '_blank');

    $attributes = $attributes->except(blank($safeHref) ? ['class', 'target'] : ['class']);
@endphp

<{{ $tag }}
    class="{{ $classes }}"
    {{
        $attributes->merge($tag === 'a' ? [
            'href'   => $safeHref,
            'target' => $opensInNewTab ? '_blank' : null,
            'rel'    => $opensInNewTab ? 'noopener noreferrer' : null,
        ] : [])
    }}
>
    @isset($media)
        {{-- The media sits outside the padding, so an image can reach the card's edge. --}}
        <div @class([
            $ui->classes('media', 'overflow-hidden rounded-t-surface'),
            '-m-4 mb-4' => $padding === 'sm',
            '-m-5 mb-5' => $padding === 'base',
            '-m-8 mb-8' => $padding === 'lg',
        ])>
            {{ $media }}
        </div>
    @endisset

    @isset($header)
        <div class="{{ $ui->classes('header', 'mb-3') }}">{{ $header }}</div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="{{ $ui->classes('footer', 'mt-4 border-t border-neutral-100 pt-4') }}">{{ $footer }}</div>
    @endisset
</{{ $tag }}>
