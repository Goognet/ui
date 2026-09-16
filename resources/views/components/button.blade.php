@props([
    'variant'      => 'default',
    'icon'         => null,
    'iconTrailing' => null,
    'size'         => 'base',
    'href'         => null,
    'external'     => false,
    'type'         => 'button',
    'square'       => false,
    'loading'      => false,
    'disabled'     => false,
    'rounded'      => false,
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    $tag = filled($href) ? 'a' : 'button';

    $safeHref = SafeUrl::href($href);

    $sizes = [
        'xs'   => $square ? 'size-7 text-xs' : 'h-7 px-2.5 text-xs',
        'sm'   => $square ? 'size-9 text-sm' : 'h-9 px-3 text-sm',
        'base' => $square ? 'size-10 text-sm' : 'h-10 px-4 text-sm',
        'lg'   => $square ? 'size-12 text-base' : 'h-12 px-6 text-base',
    ];

    /** Dark ink on the brand fills: white on them measures 4.12:1 and fails AA, neutral-950 measures 4.89:1. */
    $variants = [
        'default'   => 'border border-neutral-200 bg-white text-neutral-800 shadow-soft hover:border-neutral-300 hover:shadow-lifted',
        'primary'   => 'bg-primary text-neutral-950 shadow-soft hover:bg-primary-dark hover:shadow-lifted',
        'secondary' => 'bg-secondary text-neutral-950 shadow-soft hover:bg-secondary-dark hover:shadow-lifted',
        'filled'    => 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200',
        'ghost'     => 'bg-transparent text-neutral-700 hover:bg-neutral-100',
    ];

    $iconSizes = [
        'xs'   => 'size-3.5',
        'sm'   => 'size-4',
        'base' => 'size-4',
        'lg'   => 'size-5',
    ];

    $iconClass = $iconSizes[$size] ?? $iconSizes['base'];

    $radii = [
        'sm'   => 'rounded-md',
        'md'   => 'rounded-lg',
        'base' => 'rounded-lg',
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

    $classes = [
        'relative inline-flex cursor-pointer items-center justify-center font-medium whitespace-nowrap',
        'transition-[background-color,border-color,box-shadow,translate] duration-(--duration-base) ease-(--ease-fluid)',
        'hover:-translate-y-px active:translate-y-0 disabled:pointer-events-none disabled:opacity-50',
        $sizes[$size] ?? $sizes['base'],
        $variants[$variant] ?? $variants['default'],
        $roundedClass,
        'pointer-events-none opacity-50' => $isInert && $tag === 'a',
    ];

    $opensInNewTab = $external || $attributes->get('target') === '_blank';

    $tagAttributes = $tag === 'a'
        ? [
            'href'     => $disabled ? null : $safeHref,
            'tabindex' => $isInert ? '-1' : null,
            'target'   => $opensInNewTab ? '_blank' : null,
            'rel'      => $opensInNewTab ? 'noopener noreferrer' : null,
        ]
        : ['type' => $type, 'disabled' => $isInert];
@endphp

<{{ $tag }}
    {{ $attributes->class($classes)->merge($tagAttributes) }}
    @if ($isInert) aria-disabled="true" @endif
    @if ($loading) aria-busy="true" @endif
>
    <span @class(['inline-flex items-center gap-2', 'invisible' => $loading])>
        @if (filled($icon))
            {{ is_string($icon) ? svg($icon, $iconClass) : $icon }}
        @endif

        {{ $slot }}

        @if (filled($iconTrailing))
            {{ is_string($iconTrailing) ? svg($iconTrailing, $iconClass) : $iconTrailing }}
        @endif
    </span>

    @if ($loading)
        <svg class="absolute size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
        </svg>
    @endif
</{{ $tag }}>
