@aware(['lightbox' => false, 'masonry' => false, 'gap' => 4])

@props([
    'src'    => null,
    'source' => null,
    'alt'    => '',
    'type'   => null,
    'eager'  => false,
    'sizes'  => '(min-width: 768px) 33vw, 50vw',
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Lightbox;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;
    use Illuminate\Support\Facades\Vite;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('gallery-item');

    $safeSrc = SafeUrl::media($src);

    /** The thumb doubles as the large image when no `source` is given, which is the common case. */
    if (blank($source) && filled($safeSrc)) {
        $isRemote = preg_match('#^([a-z][a-z0-9+.\-]*:|//)#i', $safeSrc) === 1;

        $source = $isRemote
            ? $safeSrc
            : (str_contains($safeSrc, '..') ? null : Vite::asset(str_contains($safeSrc, '/') ? ltrim($safeSrc, '/') : 'resources/images/' . $safeSrc));
    }

    $href = SafeUrl::href($source);

    $opensLightbox = $lightbox !== false && filled($href);

    $spacing = [2 => 'mb-2', 3 => 'mb-3', 4 => 'mb-4', 5 => 'mb-5', 6 => 'mb-6', 8 => 'mb-8', 10 => 'mb-10', 12 => 'mb-12'];

    /**
     * In a column layout the gap only separates the columns, so the space between stacked
     * pictures is the item's own margin — and `break-inside-avoid` keeps one from being cut
     * in half at the foot of a column.
     */
    $layout = $masonry
        ? 'break-inside-avoid ' . ($spacing[(int) $gap] ?? $spacing[4])
        : 'min-w-0';

    /** A masonry picture keeps its own height; a grid one is cropped to the row's. */
    $fit = $masonry ? 'w-full' : 'h-full w-full object-cover';
@endphp

<li
    class="{{ ClassList::merge($ui->classes('base', $layout), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    @if ($opensLightbox)
        <a
            href="{{ $href }}"
            data-fslightbox="{{ Lightbox::group($lightbox, 'gallery') }}"
            @if (filled(Lightbox::type($type))) data-type="{{ Lightbox::type($type) }}" @endif
            class="{{ $ui->classes('link', 'shadow-control hover:shadow-control-hover block cursor-zoom-in overflow-hidden rounded-media transition-[translate,box-shadow] duration-(--duration-base) ease-(--ease-fluid) hover:-translate-y-0.5') }}"
        >
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <x-goognet-ui::image
                    :src="$src"
                    :alt="$alt"
                    :eager="$eager"
                    :sizes="$sizes"
                    :class="$ui->classes('image', $fit)"
                />
            @endif
        </a>
    @elseif ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <x-goognet-ui::image
            :src="$src"
            :alt="$alt"
            :eager="$eager"
            :sizes="$sizes"
            :class="$ui->classes('image', 'shadow-control rounded-media ' . $fit)"
        />
    @endif
</li>
