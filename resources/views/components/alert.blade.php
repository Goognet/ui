@props([
    'title'       => null,
    'icon'        => null,
    'variant'     => null,
    'dismissible' => false,
    'live'        => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('alert');

    $variant ??= $ui->default('variant', 'default');

    /** Neutral on purpose: the semantic colour comes from the Tailwind palette at the call site. */
    $variants = $ui->variants([
        'default' => 'border border-neutral-200 bg-white text-neutral-700',
        'filled'  => 'bg-neutral-100 text-neutral-800',
        'ghost'   => 'bg-transparent text-neutral-700',
    ]);

    $classes = ClassList::merge(
        $ui->classes('base', implode(' ', [
            'flex items-start gap-3 rounded-surface p-4 text-sm',
            $variants[$variant] ?? $variants['default'],
        ])),
        (string) $attributes->get('class'),
    );

    $iconClass = $ui->classes('icon', 'mt-0.5 size-5 shrink-0');
@endphp

<div
    class="{{ $classes }}"
    @if ($live) role="alert" @endif
    @if ($dismissible) data-alert @endif
    {{ $attributes->except('class') }}
>
    @if (filled($icon))
        {{ is_string($icon) ? svg($icon, $iconClass) : $icon }}
    @endif

    <div class="{{ $ui->classes('body', 'min-w-0 flex-1') }}">
        @if (filled($title))
            <p class="{{ $ui->classes('title', 'font-control text-neutral-900') }}">{{ $title }}</p>
        @endif

        @if ($slot->isNotEmpty())
            <div @class([$ui->classes('content', 'text-pretty'), 'mt-1' => filled($title)])>{{ $slot }}</div>
        @endif
    </div>

    @if ($dismissible)
        <button
            type="button"
            class="{{ $ui->classes('dismiss', '-m-1.5 grid size-8 shrink-0 cursor-pointer place-items-center rounded-control text-neutral-400 transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:text-neutral-700') }}"
            data-alert-dismiss
        >
            <span class="sr-only">{{ __('goognet-ui::ui.alert.dismiss') }}</span>
            {{ svg('heroicon-m-x-mark', 'size-5') }}
        </button>
    @endif
</div>
