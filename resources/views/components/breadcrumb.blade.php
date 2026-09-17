@props([
    'items'     => [],
    'separator' => 'heroicon-m-chevron-right',
    'variant'   => null,
    'size'      => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('breadcrumb');

    $variant ??= $ui->default('variant', 'primary');

    $size ??= $ui->default('size', 'sm');

    $trail = collect($items)
        ->filter(fn (mixed $item): bool => is_array($item) && filled($item['label'] ?? null))
        ->map(fn (array $item): array => [...$item, 'url' => SafeUrl::href($item['url'] ?? null)])
        ->values();

    $lastIndex = $trail->count() - 1;

    $separatorClass = $ui->classes('separator', 'opacity-50');

    /** `py-1` clears the 24px WCAG 2.2 asks of a standalone control; the trail is 20px of text otherwise. */
    $linkClass = $ui->classes('link', 'inline-flex py-1 opacity-70 hover:opacity-100');

    $currentClass = $ui->classes('current', 'font-medium');

    $sizes = $ui->sizes([
        'xs'   => 'text-xs',
        'sm'   => 'text-sm',
        'base' => 'text-base',
        'lg'   => 'text-lg',
    ]);

    $separatorSizes = [
        'xs'   => 'size-3.5',
        'sm'   => 'size-4',
        'base' => 'size-4',
        'lg'   => 'size-5',
    ];

    $textSize = $sizes[$size] ?? $sizes['sm'];

    $separatorSize = $separatorSizes[$size] ?? $separatorSizes['sm'];

    /** Schema.org expects absolute URLs; url() leaves already-absolute ones alone. */
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $trail
            ->map(function (array $item, int $index): array {
                $entry = [
                    '@type'    => 'ListItem',
                    'position' => $index + 1,
                    'name'     => $item['label'],
                ];

                if (filled($item['url'] ?? null)) {
                    $entry['item'] = url($item['url']);
                }

                return $entry;
            })
            ->all(),
    ];
@endphp

@if ($trail->isNotEmpty())
    <nav
        class="{{ ClassList::merge($ui->classes('base', ''), (string) $attributes->get('class')) }}"
        {{ $attributes->except('class')->merge(['aria-label' => 'Breadcrumb']) }}
    >
        <ol class="{{ $ui->classes('list', 'flex flex-wrap items-center gap-1.5 ' . $textSize) }}">
            @foreach ($trail as $index => $item)
                @php
                    $isCurrent = $index === $lastIndex;

                    /** Matches the icon sizing the link component uses, so both sides line up. */
                    $itemIcon = $item['icon'] ?? null;
                @endphp

                <li class="{{ $ui->classes('item', 'inline-flex items-center gap-1.5') }}">
                    @if ($index > 0)
                        <span class="{{ $separatorClass }}" aria-hidden="true">
                            {{ is_string($separator) ? svg($separator, $separatorSize) : $separator }}
                        </span>
                    @endif

                    @if (filled($item['url'] ?? null) && ! $isCurrent)
                        <x-goognet-ui::link
                            :href="$item['url']"
                            :variant="$variant"
                            :icon="$itemIcon"
                            :class="$linkClass"
                        >
                            {{ $item['label'] }}</x-goognet-ui::link>
                    @else
                        <span
                            @class([$currentClass => $isCurrent, $linkClass => ! $isCurrent])
                            @if ($isCurrent) aria-current="page" @endif
                        >
                            @if (filled($itemIcon))
                                <span class="me-1 inline-block size-[1em] align-[-0.125em]">{{ svg($itemIcon, 'size-full') }}</span>
                            @endif

                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) !!}
    </script>
@endif
