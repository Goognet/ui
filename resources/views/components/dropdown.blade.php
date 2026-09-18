@props([
    'id'      => null,
    'label'   => null,
    'icon'    => null,
    'align'   => null,
    'width'   => null,
    'variant' => null,
    'size'    => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('dropdown');

    $align ??= $ui->default('align', 'start');

    $width ??= $ui->default('width', 'min-w-56');

    /**
     * `data-menu` is what the menu script watches, so the dropdown needs no script of its own.
     *
     * The id is a prop because a trigger of your own has to name the panel it opens in its
     * `aria-controls`, and a generated id is not knowable at the call site.
     */
    $panelId = filled($id) ? (string) $id : uniqid('dropdown-');

    $alignments = ['start' => 'start-0 origin-top-left', 'end' => 'end-0 origin-top-right'];

    $panelClasses = $ui->classes('panel', implode(' ', [
        'invisible absolute top-full z-30 mt-2 translate-y-1 scale-98 rounded-surface border border-neutral-200/80 bg-white p-1.5 opacity-0 shadow-surface',
        'transition-all duration-(--duration-base) ease-(--ease-fluid)',
        'data-[state=open]:visible data-[state=open]:translate-y-0 data-[state=open]:scale-100 data-[state=open]:opacity-100',
        $alignments[$align] ?? $alignments['start'],
        is_string($width) ? $width : '',
    ]));
@endphp

<div
    class="{{ ClassList::merge($ui->classes('base', 'relative inline-block'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
    data-menu
>
    @if (isset($trigger))
        {{ $trigger }}
    @else
        <x-goognet-ui::button
            :variant="$variant"
            :size="$size"
            :icon="$icon"
            icon-trailing="heroicon-m-chevron-down"
            data-menu-dropdown
            data-state="closed"
            aria-controls="{{ $panelId }}"
            aria-expanded="false"
        >{{ $label }}</x-goognet-ui::button>
    @endif

    <div id="{{ $panelId }}" class="{{ $panelClasses }}" data-menu-dropdown-panel data-state="closed">{{ $slot }}</div>
</div>
