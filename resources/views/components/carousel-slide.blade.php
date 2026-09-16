@aware(['lightbox' => false])

@props([
    'source' => null,
    'type'   => null,
])

@php
    use Goognet\Ui\Support\Lightbox;
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    $href = SafeUrl::href($source);

    $opensLightbox = $lightbox !== false && filled($href);
@endphp

<div {{ $attributes->class(['swiper-slide h-auto']) }}>
    @if ($opensLightbox)
        <a
            href="{{ $href }}"
            data-fslightbox="{{ Lightbox::group($lightbox, 'carousel') }}"
            @if (filled(Lightbox::type($type))) data-type="{{ Lightbox::type($type) }}" @endif
            class="block cursor-zoom-in transition-[translate] duration-(--duration-base) ease-(--ease-fluid) hover:-translate-y-0.5"
        >{{ $slot }}</a>
    @else
        {{ $slot }}
    @endif
</div>
