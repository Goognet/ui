@props([
    'variant'      => null,
    'icon'         => null,
    'iconTrailing' => null,
    'size'         => null,
    'href'         => null,
    'external'     => false,
    'type'         => 'button',
    'square'       => false,
    'loading'      => false,
    'disabled'     => false,
    'rounded'      => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('button');

    $attributes = SafeUrl::attributes($attributes);

    $variant ??= $ui->default('variant', 'default');

    $size ??= $ui->default('size', 'base');

    $rounded ??= $ui->default('rounded', false);

    $tag = filled($href) ? 'a' : 'button';

    $safeHref = SafeUrl::href($href);

    $sizes = $ui->sizes([
        'xs'   => 'h-control-xs px-2.5 text-xs',
        'sm'   => 'h-control-sm px-3 text-sm',
        'base' => 'h-control px-4 text-sm',
        'lg'   => 'h-control-lg px-6 text-base',
    ]);

    $squareSizes = [
        'xs'   => 'size-control-xs text-xs',
        'sm'   => 'size-control-sm text-sm',
        'base' => 'size-control text-sm',
        'lg'   => 'size-control-lg text-base',
    ];

    /** Dark ink on the brand fills: white on them measures 4.12:1 and fails AA, neutral-950 measures 4.89:1. */
    $variants = $ui->variants([
        'default'   => 'border border-neutral-200 bg-white text-neutral-800 shadow-control hover:border-neutral-300 hover:shadow-control-hover',
        'primary'   => 'bg-primary text-neutral-950 shadow-control hover:bg-primary-dark hover:shadow-control-hover',
        'secondary' => 'bg-secondary text-neutral-950 shadow-control hover:bg-secondary-dark hover:shadow-control-hover',
        'filled'    => 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200',
        'ghost'     => 'bg-transparent text-neutral-700 hover:bg-neutral-100',
    ]);

    $iconSizes = [
        'xs'   => 'size-3.5',
        'sm'   => 'size-4',
        'base' => 'size-4',
        'lg'   => 'size-5',
    ];

    $radii = [
        'none' => 'rounded-none',
        'sm'   => 'rounded-md',
        'md'   => 'rounded-lg',
        'base' => 'rounded-control',
        'lg'   => 'rounded-xl',
        'xl'   => 'rounded-2xl',
        'full' => 'rounded-full',
    ];

    $roundedClass = match (true) {
        $rounded === true   => 'rounded-full',
        is_string($rounded) => $radii[$rounded] ?? (str_starts_with($rounded, 'rounded') ? $rounded : $radii['base']),
        default             => $radii['base'],
    };

    $isInert = $disabled || $loading;

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', array_filter([
            'relative inline-flex cursor-pointer items-center justify-center font-control whitespace-nowrap',
            'transition-[background-color,border-color,box-shadow,translate] duration-(--duration-base) ease-(--ease-fluid)',
            'hover:-translate-y-px active:translate-y-0 disabled:pointer-events-none disabled:opacity-50',
            $square ? ($squareSizes[$size] ?? $squareSizes['base']) : ($sizes[$size] ?? $sizes['base']),
            $variants[$variant] ?? $variants['default'],
            $roundedClass,
            $isInert && $tag === 'a' ? 'pointer-events-none opacity-50' : null,
        ]))),
        (string) $attributes->get('class'),
    );

    $iconClass = $ui->classes('icon', $iconSizes[$size] ?? $iconSizes['base']);

    $linkHref = $disabled ? null : $safeHref;

    /** `target` and `rel` are invalid on an `<a>` with no `href`, which is what a refused URL leaves. */
    $opensInNewTab = filled($linkHref) && ($external || $attributes->get('target') === '_blank');

    $attributes = $attributes->except(blank($linkHref) ? ['class', 'target'] : ['class']);

    $tagAttributes = $tag === 'a'
        ? [
            'href'     => $linkHref,
            'tabindex' => $isInert ? '-1' : null,
            'target'   => $opensInNewTab ? '_blank' : null,
            'rel'      => $opensInNewTab ? 'noopener noreferrer' : null,
        ]
        : ['type' => $type, 'disabled' => $isInert];
@endphp

<{{ $tag }}
    class="{{ $classes }}"
    {{ $attributes->merge($tagAttributes) }}
    @if ($isInert) aria-disabled="true" @endif
    @if ($loading) aria-busy="true" @endif
>
    <span class="{{ $ui->classes('content', 'inline-flex items-center gap-2' . ($loading ? ' invisible' : '')) }}">
        @if (filled($icon))
            {{ is_string($icon) ? svg($icon, $iconClass) : $icon }}
        @endif

        {{ $slot }}

        @if (filled($iconTrailing))
            {{ is_string($iconTrailing) ? svg($iconTrailing, $iconClass) : $iconTrailing }}
        @endif
    </span>

    @if ($loading)
        <svg
            class="{{ $ui->classes('spinner', 'absolute size-4 animate-spin') }}"
            viewBox="0 0 24 24"
            fill="none"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
        </svg>
    @endif
</{{ $tag }}>
