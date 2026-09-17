@props([
    'groups'  => [],
    'columns' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('megamenu-panel');

    $panelGroups = collect($groups)->values();

    $columnClasses = [
        1 => 'lg:grid-cols-1',
        2 => 'lg:grid-cols-2',
        3 => 'lg:grid-cols-3',
        4 => 'lg:grid-cols-4',
    ];

    $columnCount = (int) ($columns ?? min($panelGroups->count(), 4));

    $columnClass = $columnClasses[$columnCount] ?? $columnClasses[1];
@endphp

<div
    class="{{ ClassList::merge($ui->classes('base', 'mt-2 rounded-surface border border-neutral-200 bg-white p-6 shadow-surface lg:p-8'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    <div class="{{ $ui->classes('grid', 'grid gap-x-8 gap-y-6 ' . $columnClass) }}">
        @foreach ($panelGroups as $group)
            <div>
                @if (filled($group['label'] ?? null))
                    <p class="mb-3 text-xs font-semibold tracking-wide text-neutral-500 uppercase">
                        {{ $group['label'] }}
                    </p>
                @endif

                <ul class="flex flex-col gap-1">
                    @foreach ($group['children'] ?? [] as $child)
                        <li>
                            <x-goognet-ui::link
                                :href="$child['url']"
                                underline="none"
                                :class="$ui->classes('link', 'flex items-start gap-3 rounded-control p-3 transition-colors duration-(--duration-base) ease-(--ease-fluid) hover:bg-neutral-50')"
                            >
                                @if (filled($child['icon'] ?? null))
                                    <span class="{{ $ui->classes('icon', 'mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-control bg-neutral-100 text-neutral-700') }}">
                                        {{ svg($child['icon'], 'size-5') }}
                                    </span>
                                @endif

                                <span class="flex flex-col gap-0.5">
                                    <span class="font-medium text-neutral-900">{{ $child['label'] }}</span>

                                    @if (filled($child['description'] ?? null))
                                        <span class="text-sm text-neutral-500">{{ $child['description'] }}</span>
                                    @endif
                                </span>
                            </x-goognet-ui::link>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    @if ($slot->isNotEmpty())
        <div class="mt-6 border-t border-neutral-200 pt-6">{{ $slot }}</div>
    @endif
</div>
