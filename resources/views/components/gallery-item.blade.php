@aware(['lightbox' => false])

@props([
    'src'    => null,
    'source' => null,
    'alt'    => '',
    'type'   => null,
    'eager'  => false,
    'sizes'  => '(min-width: 768px) 33vw, 50vw',
])

@php
    use Goognet\Ui\Support\Lightbox;
    use Goognet\Ui\Support\SafeUrl;
    use Illuminate\Support\Facades\Vite;

    $attributes = SafeUrl::attributes($attributes);

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
@endphp

<li {{ $attributes->class(['min-w-0']) }}>
    @if ($opensLightbox)
        <a
            href="{{ $href }}"
            data-fslightbox="{{ Lightbox::group($lightbox, 'gallery') }}"
            @if (filled(Lightbox::type($type))) data-type="{{ Lightbox::type($type) }}" @endif
            class="shadow-soft hover:shadow-lifted block cursor-zoom-in overflow-hidden rounded-lg transition-[translate,box-shadow] duration-(--duration-base) ease-(--ease-fluid) hover:-translate-y-0.5"
        >
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <x-goognet-ui::image
                    :src="$src"
                    :alt="$alt"
                    :eager="$eager"
                    :sizes="$sizes"
                    class="h-full w-full object-cover"
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
            class="shadow-soft h-full w-full rounded-lg object-cover"
        />
    @endif
</li>
