@props([
    'href'         => null,
    'variant'      => null,
    'underline'    => null,
    'size'         => null,
    'icon'         => null,
    'iconTrailing' => null,
    'external'     => false,
    'label'        => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('link');

    $attributes = SafeUrl::attributes($attributes);

    $variant ??= $ui->default('variant', 'neutral');

    $underline ??= $ui->default('underline', 'hover');

    $size ??= $ui->default('size', null);

    /** Variants colour the hover only; at rest the link takes the colour of the text around it. */
    $variants = $ui->variants([
        'neutral'   => 'hover:text-neutral-900',
        'primary'   => 'hover:text-primary-ink',
        'secondary' => 'hover:text-secondary-ink',
        'white'     => 'hover:text-white',
        'none'      => '',
    ]);

    $sizes = $ui->sizes([
        'xs'   => 'text-xs',
        'sm'   => 'text-sm',
        'base' => 'text-base',
        'lg'   => 'text-lg',
    ]);

    /** Always laid out but transparent, so the colour animates; `text-decoration-line` cannot. */
    $underlines = [
        'hover'  => 'underline decoration-transparent hover:decoration-current',
        'always' => 'underline',
        'none'   => 'no-underline',
    ];

    $isIconOnly = $slot->isEmpty() && (filled($icon) || filled($iconTrailing));

    $safeHref = SafeUrl::href($href);

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', array_filter([
            'text-current underline-offset-4 decoration-1',
            'transition-[color,text-decoration-color] duration-(--duration-fast) ease-(--ease-fluid)',
            $variants[$variant] ?? $variants['neutral'],
            $isIconOnly ? 'no-underline' : ($underlines[$underline] ?? $underlines['hover']),
            filled($size) ? ($sizes[$size] ?? '') : null,
        ]))),
        (string) $attributes->get('class'),
    );

    /** `target` and `rel` are invalid on an `<a>` with no `href`, which is what a refused URL leaves. */
    $opensInNewTab = filled($safeHref) && ($external || $attributes->get('target') === '_blank');

    $attributes = $attributes->except(blank($safeHref) ? ['class', 'target'] : ['class']);

    $iconClass = $ui->classes('icon', 'inline-block size-[1em] align-[-0.125em]');
@endphp

<a
    class="{{ $classes }}"
    {{
        $attributes->merge([
            'href'   => $safeHref,
            'target' => $opensInNewTab ? '_blank' : null,
            'rel'    => $opensInNewTab ? 'noopener noreferrer' : null,
        ])
    }}
>
    @if (filled($icon))
        <span @class([$iconClass, 'me-1' => ! $isIconOnly])>{{ is_string($icon) ? svg($icon, 'size-full') : $icon }}</span>
    @endif

    {{ $slot }}

    @if (filled($label))
        <span class="sr-only">{{ $label }}</span>
    @endif

    @if (filled($iconTrailing))
        <span @class([$iconClass, 'ms-1' => ! $isIconOnly])>{{ is_string($iconTrailing) ? svg($iconTrailing, 'size-full') : $iconTrailing }}</span>
    @endif
</a>
