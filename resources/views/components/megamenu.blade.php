@props([
    'label'   => null,
    'groups'  => [],
    'columns' => null,
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    $panelId = uniqid('megamenu-');
@endphp

<div {{ $attributes }} data-menu>
    <button
        type="button"
        class="inline-flex cursor-pointer items-center gap-1 py-1.5 text-neutral-600 transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:text-neutral-900"
        data-menu-dropdown
        data-state="closed"
        aria-controls="{{ $panelId }}"
        aria-expanded="false"
    >
        {{ $label }} {{ svg('heroicon-m-chevron-down', 'size-4 text-neutral-400 transition-transform duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:rotate-180') }}
    </button>

    <div
        id="{{ $panelId }}"
        class="hidden data-[state=open]:block lg:invisible lg:absolute lg:inset-x-0 lg:top-full lg:z-30 lg:mx-auto lg:block lg:max-w-7xl lg:translate-y-1 lg:px-5 lg:opacity-0 lg:transition-all lg:duration-(--duration-base) lg:ease-(--ease-fluid) lg:data-[state=open]:visible lg:data-[state=open]:translate-y-0 lg:data-[state=open]:opacity-100"
        data-menu-dropdown-panel
        data-state="closed"
    >
        <x-goognet-ui::megamenu-panel :groups="$groups" :columns="$columns">{{ $slot }}</x-goognet-ui::megamenu-panel>
    </div>
</div>
