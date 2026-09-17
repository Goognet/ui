@aware(['name' => null])

@props([
    'label',
    'icon' => null,
    'open' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('accordion-item');

    /** The height rides on `::details-content` with `interpolate-size`; without support it snaps open. */
    $detailsClasses = [
        'group [interpolate-size:allow-keywords]',
        '[&::details-content]:h-0 [&::details-content]:overflow-hidden [&::details-content]:transition-[height,content-visibility] [&::details-content]:duration-(--duration-base) [&::details-content]:ease-(--ease-fluid) [&::details-content]:[transition-behavior:allow-discrete]',
        '[&[open]::details-content]:h-auto',
    ];

    $summaryClasses = 'flex cursor-pointer list-none items-center gap-3 py-4 text-left font-medium text-neutral-900 transition-colors duration-(--duration-base) ease-(--ease-fluid) marker:content-none hover:text-neutral-600 [&::-webkit-details-marker]:hidden';
@endphp

<details
    class="{{ $ui->classes('details', implode(' ', $detailsClasses)) }}"
    @if (filled($name)) name="{{ $name }}" @endif
    @if ($open) open @endif
>
    <summary class="{{ $ui->classes('summary', $summaryClasses) }}">
        @if (filled($icon))
            {{ is_string($icon) ? svg($icon, 'size-5 shrink-0 text-neutral-400') : $icon }}
        @endif

        {{ $label }}

        {{ svg('heroicon-m-chevron-down', 'ms-auto size-5 shrink-0 text-neutral-400 transition-transform duration-(--duration-base) ease-(--ease-fluid) group-open:rotate-180') }}
    </summary>

    <div
        class="{{ ClassList::merge($ui->classes('content', 'pb-4 text-neutral-700'), (string) $attributes->get('class')) }}"
        {{ $attributes->except('class') }}
    >
        {{ $slot }}
    </div>
</details>
