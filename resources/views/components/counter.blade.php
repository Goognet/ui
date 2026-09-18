@props([
    'value',
    'start'     => 0,
    'duration'  => 2,
    'decimals'  => 0,
    'prefix'    => '',
    'suffix'    => '',
    'separator' => '.',
    'decimal'   => ',',
    'size'      => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('counter');

    $size ??= $ui->default('size', 'base');

    $number = (float) $value;

    $decimals = max(0, min(4, (int) $decimals));

    $sizes = $ui->sizes([
        'sm'   => 'text-2xl',
        'base' => 'text-4xl',
        'lg'   => 'text-5xl',
        'xl'   => 'text-6xl',
    ]);

    /**
     * The final number is what the server prints: a page with no JavaScript, and a crawler,
     * read the real figure instead of a zero waiting for a script that never runs.
     */
    $rendered = $prefix . number_format($number, $decimals, $decimal, $separator) . $suffix;

    $config = [
        'value'     => $number,
        'start'     => (float) $start,
        'duration'  => (float) $duration,
        'decimals'  => $decimals,
        'prefix'    => (string) $prefix,
        'suffix'    => (string) $suffix,
        'separator' => (string) $separator,
        'decimal'   => (string) $decimal,
    ];
@endphp

<span
    data-counter="{{ json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) }}"
    class="{{ ClassList::merge($ui->classes('base', 'font-heading tabular-nums text-neutral-950 ' . ($sizes[$size] ?? $sizes['base'])), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>{{ $rendered }}</span>
