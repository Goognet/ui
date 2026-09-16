@props([
    'src',
    'poster'  => null,
    'loop'    => true,
    'overlay' => 'bg-neutral-950/75',
    'height'  => 'h-svh',
])

@php
    use Goognet\Ui\Support\SafeUrl;
    use Illuminate\Support\Facades\Vite;

    $attributes = SafeUrl::attributes($attributes);

    $path = str_contains($src, '/') ? ltrim($src, '/') : 'resources/videos/' . $src;

    $base = preg_replace('/\.[^.\/]+$/', '', $path);

    /** The copies the videos() plugin writes, never the master. A missing one fails loudly in Vite::asset(). */
    $sources = ['webm' => 'video/webm', 'h264.mp4' => 'video/mp4'];

    $posterUrl = filled($poster) ? Vite::asset(str_contains($poster, '/') ? ltrim($poster, '/') : 'resources/images/' . $poster) : null;
@endphp

{{-- `isolate` keeps the negative z-index inside this section instead of behind an ancestor's background. --}}
<div data-video-background {{ $attributes->class(['relative isolate w-full overflow-clip', $height]) }}>
    <video
        class="absolute inset-0 -z-20 size-full object-cover object-center"
        @if (filled($posterUrl)) poster="{{ $posterUrl }}" @endif
        autoplay
        muted
        playsinline
        preload="metadata"
        aria-hidden="true"
        tabindex="-1"
        @if ($loop) loop @endif
    >
        @foreach ($sources as $extension => $type)
            <source src="{{ Vite::asset($base . '.' . $extension) }}" type="{{ $type }}" />
        @endforeach
    </video>

    @if (filled($posterUrl))
        <div
            class="absolute inset-0 -z-30 bg-cover bg-center"
            style="background-image: url('{{ $posterUrl }}')"
            aria-hidden="true"
        ></div>
    @endif

    @if ($overlay !== false && filled($overlay))
        <div class="{{ $overlay }} absolute inset-0 -z-10" aria-hidden="true"></div>
    @endif

    {{ $slot }}
</div>
