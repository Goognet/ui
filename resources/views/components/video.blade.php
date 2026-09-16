@props([
    'url',
    'title'    => null,
    'poster'   => null,
    'quality'  => 'max',
    'ratio'    => 'video',
    'lightbox' => true,
    'eager'    => false,
])

@php
    use Goognet\Ui\Support\Lightbox;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Youtube;

    $attributes = SafeUrl::attributes($attributes);

    $id = Youtube::id($url);

    if (blank($id)) {
        throw new InvalidArgumentException('O componente de vídeo não reconheceu um vídeo do YouTube em "' . $url . '". Aceita watch?v=, youtu.be, /embed/, /shorts/, /live/ ou o id puro.');
    }

    $watchUrl = Youtube::watchUrl($id);

    $label = filled($title) ? 'Assistir ao vídeo: ' . $title : 'Assistir ao vídeo no YouTube';

    $ratios = [
        'video'  => 'aspect-video',
        'square' => 'aspect-square',
        'tall'   => 'aspect-[9/16]',
    ];

    $ratioClass = $ratios[$ratio] ?? (str_starts_with((string) $ratio, 'aspect-') ? $ratio : $ratios['video']);

    $posterClass = 'size-full object-cover object-center transition-[scale] duration-(--duration-slow) ease-(--ease-fluid) group-hover:scale-105 motion-reduce:transition-none motion-reduce:group-hover:scale-100';
@endphp

<a
    href="{{ $watchUrl }}"
    @if ($lightbox !== false) data-fslightbox="{{ Lightbox::group($lightbox, 'video-' . $id) }}" data-type="youtube" @else target="_blank" rel="noopener noreferrer" @endif
    data-video
    {{ $attributes->class(['group relative block overflow-hidden rounded-xl shadow-soft transition-shadow duration-(--duration-base) ease-(--ease-fluid) hover:shadow-lifted', $ratioClass]) }}
>
    @if (filled($poster))
        <x-goognet-ui::image :src="$poster" alt="" :eager="$eager" :class="$posterClass" />
    @else
        {{-- `maxresdefault` only exists for 720p uploads; video.js swaps in `data-video-poster` when it 404s. --}}
        <img
            src="{{ Youtube::thumbnail($id, $quality) }}"
            @if ($quality === 'max') data-video-poster="{{ Youtube::thumbnail($id, 'high') }}" @endif
            alt=""
            width="1280"
            height="720"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            decoding="async"
            class="{{ $posterClass }}"
        />
    @endif

    {{-- Not decoration: the white play button disappears on a light thumbnail without it. --}}
    <span
        class="absolute inset-0 bg-gradient-to-t from-neutral-950/50 via-neutral-950/10 to-neutral-950/25 transition-opacity duration-(--duration-base) ease-(--ease-fluid) group-hover:opacity-80"
        aria-hidden="true"
    ></span>

    <span class="absolute inset-0 grid place-items-center" aria-hidden="true">
        <span class="shadow-lifted grid size-16 place-items-center rounded-full bg-white/95 text-neutral-900 backdrop-blur-sm transition-[scale,background-color] duration-(--duration-base) ease-(--ease-fluid) group-hover:scale-110 group-hover:bg-white group-active:scale-100 motion-reduce:transition-none motion-reduce:group-hover:scale-100 sm:size-20">
            {{ svg('heroicon-s-play', 'ms-0.5 size-7 sm:size-8') }}
        </span>
    </span>

    <span class="sr-only">{{ $label }}</span>

    @if (filled($title))
        <span
            class="absolute inset-x-0 bottom-0 p-4 text-sm font-medium text-white sm:p-5 sm:text-base"
            aria-hidden="true"
        >
            {{ $title }}
        </span>
    @endif
</a>
