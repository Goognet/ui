@props([
    'variant'      => null,
    'size'         => null,
    'icon'         => null,
    'iconTrailing' => null,
    'href'         => null,
    'external'     => false,
    'dot'          => false,
    'rounded'      => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('badge');

    $attributes = SafeUrl::attributes($attributes);

    $variant ??= $ui->default('variant', 'default');

    $size ??= $ui->default('size', 'base');

    $rounded ??= $ui->default('rounded', 'full');

    $incoming = (string) $attributes->get('class');

    $safeHref = SafeUrl::href($href);

    $tag = filled($safeHref) ? 'a' : 'span';

    $sizes = $ui->sizes([
        'xs'   => 'h-5 gap-1 px-1.5 text-[11px]',
        'sm'   => 'h-6 gap-1 px-2 text-xs',
        'base' => 'h-7 gap-1.5 px-2.5 text-xs',
        'lg'   => 'h-8 gap-1.5 px-3 text-sm',
    ]);

    /** Same variant names as the button, so one vocabulary covers both. */
    $variants = $ui->variants([
        'default'   => 'border border-neutral-200 bg-white text-neutral-800 hover:bg-neutral-50',
        'primary'   => 'bg-primary text-neutral-950 hover:bg-primary-dark',
        'secondary' => 'bg-secondary text-neutral-950 hover:bg-secondary-dark',
        'filled'    => 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200',
        'ghost'     => 'bg-transparent text-neutral-700 hover:bg-neutral-100',
    ]);

    $palette = $variants[$variant] ?? $variants['default'];

    /**
     * A badge that does not link answers nothing, and one whose fill was replaced at the call
     * site would otherwise be repainted neutral the moment it is hovered — the hover belongs to
     * the fill it came with.
     */
    if ($tag !== 'a' || ClassList::setsColor($incoming, 'bg')) {
        $palette = (string) preg_replace('/\s*hover:bg-\S+/', '', $palette);
    }

    $iconSizes = [
        'xs'   => 'size-3',
        'sm'   => 'size-3.5',
        'base' => 'size-3.5',
        'lg'   => 'size-4',
    ];

    $radii = [
        'none' => 'rounded-none',
        'sm'   => 'rounded-sm',
        'md'   => 'rounded-md',
        'base' => 'rounded-control',
        'lg'   => 'rounded-lg',
        'xl'   => 'rounded-xl',
        'full' => 'rounded-full',
    ];

    $roundedClass = $radii[$rounded] ?? (str_starts_with((string) $rounded, 'rounded') ? $rounded : $radii['full']);

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', array_filter([
            'inline-flex items-center justify-center align-middle font-control whitespace-nowrap',
            'transition-[background-color,border-color] duration-(--duration-fast) ease-(--ease-fluid)',
            $sizes[$size] ?? $sizes['base'],
            $palette,
            $roundedClass,
        ]))),
        $incoming,
    );

    $opensInNewTab = filled($safeHref) && ($external || $attributes->get('target') === '_blank');

    $attributes = $attributes->except(blank($safeHref) ? ['class', 'target'] : ['class']);

    $iconClass = $ui->classes('icon', ($iconSizes[$size] ?? $iconSizes['base']) . ' shrink-0');
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
    @if ($dot)
        <span class="{{ $ui->classes('dot', 'size-1.5 shrink-0 rounded-full bg-current') }}" aria-hidden="true"></span>
    @endif

    @if (filled($icon))
        {{ is_string($icon) ? svg($icon, $iconClass) : $icon }}
    @endif

    {{ $slot }}

    @if (filled($iconTrailing))
        {{ is_string($iconTrailing) ? svg($iconTrailing, $iconClass) : $iconTrailing }}
    @endif
</{{ $tag }}>
