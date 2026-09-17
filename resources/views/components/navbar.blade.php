@props([
    'position' => null,
    'autoHide' => false,
    'border'   => true,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('navbar');

    $position ??= $ui->default('position', 'sticky');

    $incoming = (string) $attributes->get('class', '');

    $fill = ClassList::colorUnlessSet($incoming, 'bg', 'bg-white');

    /** Same reasoning for the rule: a passed border-* colour replaces the default. */
    $rule = $border
        ? trim('border-b ' . ClassList::colorUnlessSet($incoming, 'border', 'border-neutral-200'))
        : '';

    $atRest = trim(($border ? 'data-[scrolled=false]:border-transparent ' : '') . 'data-[scrolled=false]:bg-transparent');

    $positions = [
        'static'  => trim('relative ' . $rule . ' ' . $fill),
        'sticky'  => trim('sticky top-0 z-40 ' . $rule . ' ' . $fill),
        'overlay' => trim('fixed inset-x-0 top-0 z-40 ' . $rule . ' ' . $atRest . ' data-[scrolled=true]:shadow-control ' . $fill),
    ];

    $isOverlay = $position === 'overlay';

    $hidesOnScroll = $autoHide && $position !== 'static';

    $needsScript = $isOverlay || $hidesOnScroll;
@endphp

<header
    class="{{
        ClassList::merge($ui->classes('base', implode(' ', array_filter([
            $positions[$position] ?? $positions['sticky'],
            $needsScript ? 'transition-[top,background-color,border-color,box-shadow] duration-(--duration-base) ease-(--ease-fluid)' : null,
            $hidesOnScroll ? 'data-[hidden=true]:-top-full' : null,
        ]))), $incoming)
    }}"
    {{ $attributes->except('class') }}
    @if ($needsScript) data-navbar data-scrolled="false" @endif
    @if ($hidesOnScroll) data-auto-hide="true" data-hidden="false" @endif
>
    @isset($info)
        @php
            $infoClass = (string) $info->attributes->get('class', '');

            $infoRule = trim('border-b ' . \Goognet\Ui\Support\ClassList::colorUnlessSet($infoClass, 'border', 'border-neutral-200/60'));
        @endphp

        <div {{ $info->attributes->class([$ui->classes('info', 'hidden text-sm md:block ' . $infoRule)]) }}>
            <x-goognet-ui::container class="flex h-12 items-center justify-between gap-4">{{ $info }}</x-goognet-ui::container>
        </div>
    @endisset

    <x-goognet-ui::container :class="$ui->classes('bar', 'flex h-20 items-center justify-between gap-6')">
        {{ $slot }}

        @isset($cta)
            <div class="{{ $ui->classes('cta', 'hidden items-center lg:flex') }}">{{ $cta }}</div>
        @endisset
    </x-goognet-ui::container>
</header>
