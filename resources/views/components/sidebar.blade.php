@props([
    'items' => [],
    'label',
    'title'  => 'Nesta página',
    'sticky' => true,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Navigation;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('sidebar');

    if (blank($label)) {
        throw new InvalidArgumentException('O componente sidebar exige um label, que nomeia a navegação: label="Seções desta política". Uma página costuma ter várias, e o leitor de tela as lista por esse nome.');
    }

    $links = collect($items)
        ->map(function (mixed $item, string | int $key): ?array {
            if (is_array($item)) {
                $url = SafeUrl::href(Navigation::resolve($item));

                return filled($url) && filled($item['label'] ?? null)
                    ? ['url' => $url, 'label' => $item['label'], 'anchor' => str_starts_with((string) $url, '#')]
                    : null;
            }

            return is_string($key)
                ? ['url' => '#' . $key, 'label' => $item, 'anchor' => true]
                : null;
        })
        ->filter()
        ->values();

    $stickyClass = $sticky
        ? 'lg:sticky lg:top-[calc(var(--navbar-height,0px)+1.5rem)] lg:self-start lg:flex lg:max-h-[calc(100svh-var(--navbar-height,0px)-3rem)] lg:flex-col'
        : '';

    $itemClass = $ui->classes('item', implode(' ', [
        '-ms-px border-s-2 border-transparent py-1.5 ps-3.5 text-sm text-neutral-600',
        'transition-colors duration-(--duration-fast) ease-(--ease-fluid)',
        'hover:border-primary hover:text-neutral-900',
        /** Set by sidebar.js as the reader moves; the colour is the same one hover promises. */
        'data-[current]:border-primary data-[current]:font-medium data-[current]:text-neutral-900',
    ]));
@endphp

@if ($links->isNotEmpty())
    <aside
        class="{{ ClassList::merge($ui->classes('base', 'py-10 ' . $stickyClass), (string) $attributes->get('class')) }}"
        {{ $attributes->except('class') }}
    >
        @if (filled($title))
            <p class="{{ $ui->classes('title', 'text-[11px] font-semibold tracking-[0.1em] text-neutral-500 uppercase') }}">
                {{ $title }}
            </p>
        @endif

        <nav
            aria-label="{{ $label }}"
            @class([
                $ui->classes('nav', 'flex flex-col border-s border-neutral-200'),
                'mt-3.5'                                                               => filled($title),
                'min-h-0 overflow-y-auto overscroll-contain [scrollbar-gutter:stable]' => $sticky,
            ])
            @if ($links->every(fn (array $link): bool => $link['anchor'])) data-sidebar @endif
        >
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}" class="{{ $itemClass }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>
    </aside>
@endif
