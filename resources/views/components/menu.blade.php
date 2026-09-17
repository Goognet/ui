@props([
    'items' => null,
    'label' => 'Menu principal',
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Navigation;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('menu');

    $resolveUrls = function (array $items) use (&$resolveUrls): array {
        return collect($items)
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(function (array $item) use (&$resolveUrls): array {
                $item['url'] = Navigation::resolve($item);

                foreach (['children', 'groups'] as $nested) {
                    if (filled($item[$nested] ?? null)) {
                        $item[$nested] = $resolveUrls($item[$nested]);
                    }
                }

                return $item;
            })
            ->all();
    };

    $menu = collect($resolveUrls((array) ($items ?? config('goognet-ui.menu', []))))->values();

    /** Ids wire the toggles to their panels through aria-controls; they must be unique per render. */
    $panelId = uniqid('menu-panel-');

    /** The page the visitor is on, so the bar can say where they are. */
    $currentUrl = rtrim(request()->url(), '/');

    $root = rtrim(url('/'), '/');

    $matches = function (?string $url) use ($currentUrl, $root): bool {
        if (blank($url)) {
            return false;
        }

        $target = rtrim(url($url), '/');

        return $target === $currentUrl
            || ($target !== $root && str_starts_with($currentUrl, $target . '/'));
    };

    $isCurrent = function (array $item) use ($matches): bool {
        if (array_key_exists('current', $item)) {
            return (bool) $item['current'];
        }

        if (filled($item['url'] ?? null)) {
            return $matches($item['url']);
        }

        return collect($item['groups'] ?? [])
            ->flatMap(fn (array $group): array => $group['children'] ?? [])
            ->merge($item['children'] ?? [])
            ->contains(fn (array $child): bool => $matches($child['url'] ?? null));
    };

    /**
     * A badge is a string for the common case and an array when it needs a colour: it is handed
     * to `x-ui.badge`, so the vocabulary is the one the rest of the library already uses.
     *
     * @return array{label: string, variant: string}|null
     */
    $badgeOf = function (array $item): ?array {
        $badge = $item['badge'] ?? null;

        if (blank($badge)) {
            return null;
        }

        return is_array($badge)
            ? ['label' => (string) ($badge['label'] ?? ''), 'variant' => (string) ($badge['variant'] ?? 'filled')]
            : ['label' => (string) $badge, 'variant' => 'filled'];
    };

    $itemClass = $ui->classes('item', implode(' ', [
        'relative inline-flex items-center gap-1 py-1.5 text-neutral-600',
        'transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:text-neutral-900',
        'after:bg-primary after:absolute after:inset-x-0 after:-bottom-0.5 after:h-0.5 after:origin-center after:scale-x-0 after:rounded-full',
        'after:transition-transform after:duration-(--duration-base) after:ease-(--ease-fluid) hover:after:scale-x-100',
    ]));

    $currentClass = $ui->classes('current', 'text-neutral-900 after:scale-x-100');

    $triggerClass = $ui->classes('trigger', $itemClass . ' cursor-pointer');

    $chevronClass = $ui->classes('chevron', 'size-4 text-neutral-400 transition-transform duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:rotate-180');

    /** Drawer rows are the primary target on a phone: 44px tall, not 36px. */
    $drawerLinkClass = $ui->classes('drawer-link', 'flex items-center rounded-control px-3 py-2.5 text-neutral-700 transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-neutral-100');

    /** An item is a megamenu when it carries groups, a dropdown when it carries children. */
    $groupsOf = fn (array $item) => collect($item['groups'] ?? []);

    $childrenOf = fn (array $item) => collect($item['children'] ?? []);
@endphp

<nav
    aria-label="{{ $label }}"
    class="{{ ClassList::merge($ui->classes('base', 'flex items-center'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
    data-menu
>
    <ul class="{{ $ui->classes('list', 'hidden items-center gap-6 lg:flex') }}">
        @foreach ($menu as $index => $item)
            @php
                $groups     = $groupsOf($item);
                $children   = $childrenOf($item);
                $dropdownId = $panelId . '-dropdown-' . $index;
                $current    = $isCurrent($item);
                $badge      = $badgeOf($item);
            @endphp

            {{-- A megamenu panel spans the header, so its item must not create a positioning context. --}}
            <li @class(['relative' => $children->isNotEmpty()])>
                @if ($groups->isNotEmpty())
                    <button
                        type="button"
                        @class([$triggerClass, $currentClass => $current])
                        data-menu-dropdown
                        data-state="closed"
                        aria-controls="{{ $dropdownId }}"
                        aria-expanded="false"
                    >
                        {{ $item['label'] }}

                        @if (filled($badge))
                            <x-goognet-ui::badge
                                size="xs"
                                :variant="$badge['variant']"
                            >{{ $badge['label'] }}</x-goognet-ui::badge>
                        @endif

                        {{ svg('heroicon-m-chevron-down', $chevronClass) }}
                    </button>

                    <div
                        id="{{ $dropdownId }}"
                        class="{{ $ui->classes('megamenu', 'invisible absolute inset-x-0 top-full z-30 mx-auto max-w-page translate-y-1 px-5 opacity-0 transition-all duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:visible data-[state=open]:translate-y-0 data-[state=open]:opacity-100') }}"
                        data-menu-dropdown-panel
                        data-state="closed"
                    >
                        <x-goognet-ui::megamenu-panel :groups="$groups" :columns="$item['columns'] ?? null" />
                    </div>
                @elseif ($children->isNotEmpty())
                    <button
                        type="button"
                        @class([$triggerClass, $currentClass => $current])
                        data-menu-dropdown
                        data-state="closed"
                        aria-controls="{{ $dropdownId }}"
                        aria-expanded="false"
                    >
                        {{ $item['label'] }}

                        @if (filled($badge))
                            <x-goognet-ui::badge
                                size="xs"
                                :variant="$badge['variant']"
                            >{{ $badge['label'] }}</x-goognet-ui::badge>
                        @endif

                        {{ svg('heroicon-m-chevron-down', $chevronClass) }}
                    </button>

                    <ul
                        id="{{ $dropdownId }}"
                        class="{{ $ui->classes('dropdown', 'shadow-surface invisible absolute top-full left-0 z-30 mt-3 min-w-64 origin-top translate-y-1 scale-98 rounded-surface border border-neutral-200/80 bg-white p-1.5 opacity-0 transition-all duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:visible data-[state=open]:translate-y-0 data-[state=open]:scale-100 data-[state=open]:opacity-100') }}"
                        data-menu-dropdown-panel
                        data-state="closed"
                    >
                        @foreach ($children as $child)
                            <li>
                                <x-goognet-ui::link
                                    :href="$child['url']"
                                    :icon="$child['icon'] ?? null"
                                    underline="none"
                                    :class="$drawerLinkClass . ' gap-3 items-start'"
                                >
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
                @else
                    <x-goognet-ui::link
                        :href="$item['url']"
                        :icon="$item['icon'] ?? null"
                        underline="none"
                        :class="$itemClass . ($current ? ' ' . $currentClass : '')"
                        :aria-current="$current ? 'page' : null"
                    >
                        {{ $item['label'] }}

                        @if (filled($badge))
                            <x-goognet-ui::badge
                                size="xs"
                                :variant="$badge['variant']"
                            >{{ $badge['label'] }}</x-goognet-ui::badge>
                        @endif
                    </x-goognet-ui::link>
                @endif
            </li>
        @endforeach
    </ul>

    <button
        type="button"
        class="{{ $ui->classes('toggle', 'inline-flex size-10 cursor-pointer items-center justify-center lg:hidden') }}"
        data-menu-toggle
        aria-controls="{{ $panelId }}"
        aria-expanded="false"
    >
        <span class="sr-only">Abrir menu</span>
        {{ svg('heroicon-o-bars-3', 'size-6') }}
    </button>

    <div
        class="{{ $ui->classes('overlay', 'invisible fixed inset-0 z-40 bg-neutral-950/50 opacity-0 transition-opacity duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:visible data-[state=open]:opacity-100 lg:hidden') }}"
        data-menu-overlay
        data-state="closed"
    ></div>

    <div
        id="{{ $panelId }}"
        class="{{ $ui->classes('drawer', 'invisible fixed inset-y-0 right-0 z-50 flex w-80 max-w-[85vw] translate-x-full flex-col gap-6 overflow-y-auto bg-white p-6 transition-transform duration-(--duration-base) ease-(--ease-fluid) data-[state=open]:visible data-[state=open]:translate-x-0 lg:hidden') }}"
        data-menu-panel
        data-state="closed"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $label }}"
    >
        <button
            type="button"
            class="ms-auto inline-flex size-10 cursor-pointer items-center justify-center"
            data-menu-close
        >
            <span class="sr-only">Fechar menu</span>
            {{ svg('heroicon-o-x-mark', 'size-6') }}
        </button>

        <ul class="flex flex-col gap-1">
            @foreach ($menu as $index => $item)
                @php
                    $groups      = $groupsOf($item);
                    $children    = $childrenOf($item);
                    $accordionId = $panelId . '-accordion-' . $index;
                    $current     = $isCurrent($item);
                    $badge       = $badgeOf($item);
                @endphp

                <li>
                    @if ($groups->isNotEmpty() || $children->isNotEmpty())
                        <button
                            type="button"
                            class="flex w-full cursor-pointer items-center justify-between gap-2 rounded-md px-3 py-2 text-left transition-colors duration-(--duration-base) ease-(--ease-fluid) hover:bg-neutral-50"
                            data-menu-dropdown
                            data-state="closed"
                            aria-controls="{{ $accordionId }}"
                            aria-expanded="false"
                        >
                            <span class="inline-flex items-center gap-2">
                                {{ $item['label'] }}

                                @if (filled($badge))
                                    <x-goognet-ui::badge size="xs" :variant="$badge['variant']">
                                        {{ $badge['label'] }}</x-goognet-ui::badge>
                                @endif
                            </span>

                            {{ svg('heroicon-m-chevron-down', $chevronClass) }}
                        </button>

                        {{-- In the drawer a megamenu is just its groups stacked, so both shapes share one accordion. --}}
                        <div
                            id="{{ $accordionId }}"
                            class="hidden ps-3 data-[state=open]:block"
                            data-menu-dropdown-panel
                            data-state="closed"
                        >
                            @foreach ($groups->isNotEmpty() ? $groups : collect([['children' => $children]]) as $group)
                                @if (filled($group['label'] ?? null))
                                    <p class="mt-3 mb-1 px-3 text-xs font-semibold tracking-wide text-neutral-500 uppercase">
                                        {{ $group['label'] }}
                                    </p>
                                @endif

                                <ul class="flex flex-col gap-1">
                                    @foreach ($group['children'] ?? [] as $child)
                                        <li>
                                            <x-goognet-ui::link
                                                :href="$child['url']"
                                                :icon="$child['icon'] ?? null"
                                                underline="none"
                                                :class="$drawerLinkClass . ' text-neutral-700'"
                                            >
                                                {{ $child['label'] }}</x-goognet-ui::link>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    @else
                        <x-goognet-ui::link
                            :href="$item['url']"
                            :icon="$item['icon'] ?? null"
                            underline="none"
                            :class="$drawerLinkClass . ($current ? ' bg-neutral-100 font-medium text-neutral-900' : '')"
                            :aria-current="$current ? 'page' : null"
                        >
                            {{ $item['label'] }}

                            @if (filled($badge))
                                <x-goognet-ui::badge size="xs" :variant="$badge['variant']" class="ms-2">
                                    {{ $badge['label'] }}</x-goognet-ui::badge>
                            @endif
                        </x-goognet-ui::link>
                    @endif
                </li>
            @endforeach
        </ul>

        @if ($slot->isNotEmpty())
            <div class="mt-auto border-t border-neutral-200 pt-6">{{ $slot }}</div>
        @endif
    </div>
</nav>
