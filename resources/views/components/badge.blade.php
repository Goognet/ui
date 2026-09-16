@props([
    'variant'      => 'default',
    'size'         => 'base',
    'icon'         => null,
    'iconTrailing' => null,
    'href'         => null,
    'external'     => false,
    'dot'          => false,
    'rounded'      => 'full',
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    $tag = filled($href) ? 'a' : 'span';

    $incoming = (string) $attributes->get('class');

    $sizes = [
        'xs'   => 'h-5 gap-1 px-1.5 text-[11px]',
        'sm'   => 'h-6 gap-1 px-2 text-xs',
        'base' => 'h-7 gap-1.5 px-2.5 text-xs',
        'lg'   => 'h-8 gap-1.5 px-3 text-sm',
    ];

    /** @var array<string, array{fill: string, ink: string, edge: string, hover: string}> */
    $variants = [
        'default'   => ['fill' => 'bg-white', 'ink' => 'text-neutral-800', 'edge' => 'border-neutral-200', 'hover' => 'hover:bg-neutral-50'],
        'primary'   => ['fill' => 'bg-primary', 'ink' => 'text-neutral-950', 'edge' => '', 'hover' => 'hover:bg-primary-dark'],
        'secondary' => ['fill' => 'bg-secondary', 'ink' => 'text-neutral-950', 'edge' => '', 'hover' => 'hover:bg-secondary-dark'],
        'filled'    => ['fill' => 'bg-neutral-100', 'ink' => 'text-neutral-800', 'edge' => '', 'hover' => 'hover:bg-neutral-200'],
        'ghost'     => ['fill' => 'bg-transparent', 'ink' => 'text-neutral-700', 'edge' => '', 'hover' => 'hover:bg-neutral-100'],
    ];

    $palette = $variants[$variant] ?? $variants['default'];

    $iconSizes = [
        'xs'   => 'size-3',
        'sm'   => 'size-3.5',
        'base' => 'size-3.5',
        'lg'   => 'size-4',
    ];

    $iconClass = $iconSizes[$size] ?? $iconSizes['base'];

    $radii = [
        'sm'   => 'rounded-sm',
        'md'   => 'rounded-md',
        'base' => 'rounded-md',
        'lg'   => 'rounded-lg',
        'xl'   => 'rounded-xl',
        'full' => 'rounded-full',
    ];

    $roundedClass = $radii[$rounded] ?? (str_starts_with((string) $rounded, 'rounded') ? $rounded : $radii['full']);

    $classes = [
        'inline-flex items-center justify-center align-middle font-medium whitespace-nowrap',
        'transition-[background-color,border-color] duration-(--duration-fast) ease-(--ease-fluid)',
        $sizes[$size] ?? $sizes['base'],
        ClassList::colorUnlessSet($incoming, 'bg', $palette['fill']),
        ClassList::colorUnlessSet($incoming, 'text', $palette['ink']),
        filled($palette['edge']) ? 'border ' . ClassList::colorUnlessSet($incoming, 'border', $palette['edge']) : '',
        $roundedClass,
        $tag === 'a' && ! ClassList::setsColor($incoming, 'bg') ? ClassList::colorUnlessSet($incoming, 'bg', $palette['hover'], 'hover') : '',
    ];

    $opensInNewTab = $external || $attributes->get('target') === '_blank';

    $tagAttributes = $tag === 'a'
        ? [
            'href'   => SafeUrl::href($href),
            'target' => $opensInNewTab ? '_blank' : null,
            'rel'    => $opensInNewTab ? 'noopener noreferrer' : null,
        ]
        : [];
@endphp

<{{ $tag }} {{ $attributes->class($classes)->merge($tagAttributes) }}>
    @if ($dot)
        <span class="size-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
    @endif

    @if (filled($icon))
        {{ is_string($icon) ? svg($icon, $iconClass . ' shrink-0') : $icon }}
    @endif

    {{ $slot }}

    @if (filled($iconTrailing))
        {{ is_string($iconTrailing) ? svg($iconTrailing, $iconClass . ' shrink-0') : $iconTrailing }}
    @endif
</{{ $tag }}>
