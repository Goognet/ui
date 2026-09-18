@props([
    'text'      => null,
    'placement' => null,
    'focusable' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('tooltip');

    $placement ??= $ui->default('placement', 'top');

    $tooltipId = uniqid('tooltip-');

    $placements = [
        'top'    => 'bottom-full left-1/2 mb-2 -translate-x-1/2',
        'bottom' => 'top-full left-1/2 mt-2 -translate-x-1/2',
        'left'   => 'right-full top-1/2 mr-2 -translate-y-1/2',
        'right'  => 'left-full top-1/2 ml-2 -translate-y-1/2',
    ];

    /**
     * Shown on hover and on focus alike: a trigger reached by keyboard never receives a
     * pointer, and a tooltip only the mouse can open is a tooltip half the visitors never see.
     */
    $bubbleClasses = $ui->classes('bubble', implode(' ', [
        'pointer-events-none absolute z-30 w-max max-w-64 rounded-control bg-neutral-900 px-2.5 py-1.5 text-xs text-white shadow-control',
        'invisible opacity-0 transition-[opacity,visibility] duration-(--duration-fast) ease-(--ease-fluid)',
        'group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100',
        $placements[$placement] ?? $placements['top'],
    ]));
@endphp

@if (blank($text))
    {{ $slot }}
@else
    <span
        class="{{ ClassList::merge($ui->classes('base', 'group relative inline-flex'), (string) $attributes->get('class')) }}"
        @if ($focusable) tabindex="0" aria-describedby="{{ $tooltipId }}" @endif
        {{ $attributes->except('class') }}
    >
        {{ $slot }}

        <span id="{{ $tooltipId }}" role="tooltip" class="{{ $bubbleClasses }}">{{ $text }}</span>
    </span>
@endif
