@props([
    'src',
    'alt'    => '',
    'widths' => null,
    'sizes'  => '100vw',
    'eager'  => false,
])

@php
    use Goognet\Ui\Support\SafeUrl;
    use Illuminate\Support\Facades\Vite;

    $attributes = SafeUrl::attributes($attributes);

    $safeSrc = SafeUrl::media($src);

    $isRemote = filled($safeSrc) && preg_match('#^([a-z][a-z0-9+.\-]*:|//)#i', $safeSrc) === 1;

    /** A local path climbing out of the project is refused before it reaches the filesystem. */
    $isLocal = filled($safeSrc) && ! $isRemote && ! str_contains($safeSrc, '..');

    $path = $isLocal ? (str_contains($safeSrc, '/') ? ltrim($safeSrc, '/') : 'resources/images/' . $safeSrc) : null;

    /** Only jpg and png get companions from the images() plugin in vite.config.js. */
    $isRaster = $isLocal && preg_match('/\.(jpe?g|png)$/i', (string) $path) === 1;

    $file = $isRaster ? base_path((string) $path) : null;

    $dimensions = filled($file) && is_file($file) ? getimagesize($file) : null;

    $withSuffix = fn (string $suffix): string => (string) preg_replace('/\.[^.\/]+$/', $suffix, (string) $path);

    /** The widths on disk, not the ones the plugin was asked for: it never enlarges a source. */
    $available = $isRaster
        ? collect(glob(preg_replace('/\.[^.\/]+$/', '-*', (string) $file) . '.webp') ?: [])
            ->map(fn (string $variant): int => (int) preg_replace('/^.*-(\d+)\.webp$/', '$1', $variant))
            ->filter()
            ->when(filled($widths), fn (Illuminate\Support\Collection $found) => $found->intersect($widths))
            ->sort()
            ->values()
        : collect();

    $srcset = fn (string $extension): string => $available
        ->map(fn (int $width): string => Vite::asset($withSuffix("-{$width}.{$extension}")) . ' ' . $width . 'w')
        ->push(Vite::asset($withSuffix('.' . $extension)) . ' ' . ($dimensions[0] ?? 0) . 'w')
        ->implode(', ');

    $imageAttributes = $attributes->merge([
        'alt'           => $alt,
        'width'         => $dimensions[0] ?? null,
        'height'        => $dimensions[1] ?? null,
        'decoding'      => 'async',
        'loading'       => $eager ? 'eager' : 'lazy',
        'fetchpriority' => $eager ? 'high' : null,
    ]);
@endphp

@if ($isRaster && filled($dimensions))
    <picture>
        <source type="image/webp" srcset="{{ $srcset('webp') }}" sizes="{{ $sizes }}" />

        <img
            src="{{ Vite::asset((string) $path) }}"
            srcset="{{ $srcset(pathinfo((string) $path, PATHINFO_EXTENSION)) }}"
            sizes="{{ $sizes }}"
            {{ $imageAttributes }}
        />
    </picture>
@elseif ($isRemote)
    <img src="{{ $safeSrc }}" {{ $imageAttributes }} />
@elseif ($isLocal)
    <img src="{{ Vite::asset((string) $path) }}" {{ $imageAttributes }} />
@endif
