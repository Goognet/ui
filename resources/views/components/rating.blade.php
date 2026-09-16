@props([
    'name'      => null,
    'value'     => null,
    'max'       => 5,
    'size'      => 'base',
    'shape'     => 'star',
    'label'     => null,
    'clearable' => false,
    'disabled'  => false,
])

@php
    use Goognet\Ui\Support\SafeUrl;
    use Illuminate\Support\Str;

    $attributes = SafeUrl::attributes($attributes);

    $isInput = filled($name);

    /** Capped: every point is rendered, and an unbounded `max` is an unbounded page on the server. */
    $max = min(10, max(1, (int) $max));

    $sizes = [
        'xs'   => 'size-4',
        'sm'   => 'size-5',
        'base' => 'size-6',
        'lg'   => 'size-7',
        'xl'   => 'size-9',
    ];

    /** Outline for empty, solid for full: colour alone measures 1.57:1, under the 3:1 a graphic owes. */
    $shapes = [
        'star'  => ['on' => 'heroicon-s-star', 'off' => 'heroicon-o-star'],
        'heart' => ['on' => 'heroicon-s-heart', 'off' => 'heroicon-o-heart'],
    ];

    $iconClass = $sizes[$size] ?? $sizes['base'];

    $icon = $shapes[$shape] ?? $shapes['star'];

    $number = fn (float $rating): string => rtrim(rtrim(number_format($rating, 1, ',', ''), '0'), ',');
@endphp

@if ($isInput)
    @php
        $selected = filled($value) ? (int) $value : null;

        $group = 'rating-' . Str::slug((string) $name) . '-' . Str::random(6);
    @endphp

    {{-- Stars run from max down to 1 and are flipped back: a checked input only reaches later siblings. --}}
    <fieldset
        data-rating
        {{ $attributes->class(['inline-flex flex-row-reverse items-center justify-end', 'opacity-50' => $disabled]) }}
        @disabled($disabled)
    >
        <legend class="sr-only">{{ $label ?? 'Nota de 1 a ' . $max }}</legend>

        @foreach (range($max, 1) as $star)
            <input
                type="radio"
                id="{{ $group . '-' . $star }}"
                name="{{ $name }}"
                value="{{ $star }}"
                class="peer sr-only"
                @checked($selected === $star)
                @disabled($disabled)
            />

            <label
                for="{{ $group . '-' . $star }}"
                class="{{ $disabled ? '' : 'cursor-pointer' }} grid text-neutral-500 transition-colors duration-(--duration-fast) ease-(--ease-fluid)"
                title="{{ $star }}"
            >
                <span class="sr-only">{{ $star }} de {{ $max }}</span>

                <span class="col-start-1 row-start-1" aria-hidden="true">{{ svg($icon['off'], $iconClass) }}</span>

                <span class="rating-on text-primary col-start-1 row-start-1" aria-hidden="true">
                    {{ svg($icon['on'], $iconClass) }}
                </span>
            </label>
        @endforeach

        @if ($clearable)
            <input
                type="radio"
                name="{{ $name }}"
                value=""
                class="sr-only"
                @checked($selected === null)
                @disabled($disabled)
            />
        @endif
    </fieldset>
@else
    @php
        $rating = min((float) $value, (float) $max);

        $fill = max(0, $rating) / $max * 100;
    @endphp

    <span
        {{ $attributes->class('relative inline-flex align-middle') }}
        role="img"
        aria-label="{{ $label ?? $number($rating) . ' de ' . $number((float) $max) }}"
    >
        <span class="flex text-neutral-500" aria-hidden="true">
            @foreach (range(1, $max) as $star)
                {{ svg($icon['off'], $iconClass) }}
            @endforeach
        </span>

        <span
            class="text-primary absolute inset-y-0 start-0 flex overflow-hidden"
            style="width: {{ round($fill, 2) }}%"
            aria-hidden="true"
        >
            @foreach (range(1, $max) as $star)
                {{ svg($icon['on'], $iconClass . ' shrink-0') }}
            @endforeach
        </span>
    </span>
@endif
