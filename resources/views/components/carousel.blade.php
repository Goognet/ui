@props([
    'perView'        => 1,
    'gap'            => 16,
    'autoplay'       => false,
    'autoHeight'     => false,
    'loop'           => null,
    'pagination'     => false,
    'dynamicBullets' => false,
    'navigation'     => false,
    'lightbox'       => false,
    'label'          => 'Carrossel',
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** Tailwind's own breakpoints, so the carousel changes shape where the rest of the page does. */
    $breakpoints = [
        'base' => 0,
        'sm'   => 640,
        'md'   => 768,
        'lg'   => 1024,
        'xl'   => 1280,
        '2xl'  => 1536,
    ];

    $byBreakpoint = function (mixed $value) use ($breakpoints): array {
        $map = is_array($value) ? $value : ['base' => $value];

        return collect($map)
            ->filter(fn (mixed $_, string $key): bool => isset($breakpoints[$key]))
            ->mapWithKeys(fn (mixed $at, string $key): array => [$breakpoints[$key] => $at])
            ->sortKeys()
            ->all();
    };

    $slides = $byBreakpoint($perView);

    $gaps = $byBreakpoint($gap);

    $config = [
        'slidesPerView' => $slides[0] ?? 1,
        'spaceBetween'  => $gaps[0] ?? 16,
        'loop'          => $loop,
        /** `filled(false)` is true, so the off switch has to be checked before the cast. */
        'autoplay' => match (true) {
            $autoplay === true                    => 4000,
            $autoplay === false, blank($autoplay) => false,
            default                               => (int) $autoplay,
        },
        'pagination'     => (bool) $pagination || (bool) $dynamicBullets,
        'dynamicBullets' => (bool) $dynamicBullets,
        'navigation'     => (bool) $navigation,

        'autoHeight' => (bool) $autoHeight,

        /** Swiper keys its breakpoints by min-width, and 0 is already the base above. */
        'breakpoints' => collect($breakpoints)
            ->values()
            ->filter()
            ->mapWithKeys(fn (int $width): array => [$width => array_filter([
                'slidesPerView' => $slides[$width] ?? null,
                'spaceBetween'  => $gaps[$width] ?? null,
            ], fn (mixed $value): bool => ! is_null($value))])
            ->filter(fn (array $at): bool => filled($at))
            ->all(),
    ];
@endphp

<div
    data-carousel="{{ json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) }}"
    role="region"
    aria-roledescription="carousel"
    aria-label="{{ $label }}"
    {{ $attributes }}
>
    <div class="swiper">
        <div class="swiper-wrapper">{{ $slot }}</div>

        @if ($navigation)
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        @endif
    </div>

    @if ($config['pagination'])
        <div class="relative mt-4 h-6">
            <div class="swiper-pagination"></div>
        </div>
    @endif
</div>
