@aware(['lightbox' => false])

@props([
    'source' => null,
    'type'   => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Lightbox;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('carousel-slide');

    $href = SafeUrl::href($source);

    $opensLightbox = $lightbox !== false && filled($href);
@endphp

<div
    class="{{ ClassList::merge($ui->classes('base', 'swiper-slide h-auto'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    @if ($opensLightbox)
        <a
            href="{{ $href }}"
            data-fslightbox="{{ Lightbox::group($lightbox, 'carousel') }}"
            @if (filled(Lightbox::type($type))) data-type="{{ Lightbox::type($type) }}" @endif
            class="{{ $ui->classes('link', 'block cursor-zoom-in transition-[translate] duration-(--duration-base) ease-(--ease-fluid) hover:-translate-y-0.5') }}"
        >{{ $slot }}</a>
    @else
        {{ $slot }}
    @endif
</div>
