@props([
    'src'   => null,
    'title' => 'Mapa de localização',
    'ratio' => 'video',
    'eager' => false,
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** An empty `src` loads the current page inside itself, so a refused or missing address emits nothing. */
    $frameSrc = SafeUrl::frame($src ?? config('goognet-ui.location.map'));

    $ratios = [
        'video'  => 'aspect-video',
        'square' => 'aspect-square',
        'wide'   => 'aspect-[21/9]',
        'tall'   => 'aspect-[3/4]',
    ];

    $ratioClass = match (true) {
        $ratio === false, blank($ratio) => '',
        default                         => $ratios[$ratio] ?? (str_starts_with((string) $ratio, 'aspect-') ? $ratio : $ratios['video']),
    };
@endphp

@if (filled($frameSrc))
    <iframe
        src="{{ $frameSrc }}"
        title="{{ $title }}"
        loading="{{ $eager ? 'eager' : 'lazy' }}"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen
        {{-- No `allow-top-navigation`: a click inside the frame cannot send the whole page elsewhere. --}}
        {{ $attributes->class(['w-full border-0', $ratioClass])->merge(['sandbox' => 'allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox']) }}
    ></iframe>
@endif
