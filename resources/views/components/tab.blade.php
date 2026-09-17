@aware(['name'])

@props([
    'label',
    'icon'    => null,
    'checked' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('tab');

    if (blank($name)) {
        throw new InvalidArgumentException('A tab must sit inside a tabs component, whose name groups the radios.');
    }

    $triggerId = uniqid('tab-');

    $panelId = $triggerId . '-panel';

    /** With nothing checked the first tab leads; `:first-of-type` counts the first label and the first div. */
    $triggerClasses = [
        'inline-flex cursor-pointer items-center gap-2 border-b-2 border-transparent px-4 py-2.5 text-sm font-medium whitespace-nowrap text-neutral-500 transition-colors duration-(--duration-base) ease-(--ease-fluid) outline-offset-2',
        'hover:border-neutral-300 hover:text-neutral-800',
        'has-[:checked]:border-primary has-[:checked]:text-neutral-950',
        'has-[:focus-visible]:[outline:2px_solid_var(--color-primary)] has-[:focus-visible]:outline-offset-2',
        '[div:not(:has(:checked))>&:first-of-type]:border-primary [div:not(:has(:checked))>&:first-of-type]:text-neutral-950',
    ];

    $panelClasses = [
        'order-last hidden w-full pt-6',
        '[label:has(:checked)+&]:block',
        '[div:not(:has(:checked))>&:first-of-type]:block',
    ];
@endphp

<label id="{{ $triggerId }}" class="{{ $ui->classes('trigger', implode(' ', $triggerClasses)) }}">
    <input type="radio" name="{{ $name }}" class="sr-only" aria-controls="{{ $panelId }}" @checked($checked) />

    @if (filled($icon))
        {{ is_string($icon) ? svg($icon, 'size-4') : $icon }}
    @endif

    {{ $label }}
</label>

<div
    id="{{ $panelId }}"
    role="region"
    aria-labelledby="{{ $triggerId }}"
    class="{{ ClassList::merge($ui->classes('panel', implode(' ', $panelClasses)), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    {{ $slot }}
</div>
