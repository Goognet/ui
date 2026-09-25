@props([
    'callout'       => true,
    'calloutTitle'  => 'Precisa de um orçamento?',
    'calloutText'   => 'Resposta no mesmo dia útil.',
    'calloutAction' => 'Solicite um orçamento',
    'description'   => null,
    'validator'     => true,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Navigation;
    use Goognet\Ui\Support\Phone;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Whatsapp;
    use Goognet\Ui\Ui;
    use Illuminate\Support\Str;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('footer');

    $company = (array) config('goognet-ui.company', []);

    $agency = (array) config('goognet-ui.agency', []);

    $menu = collect(config('goognet-ui.menu', []))
        ->filter(fn (mixed $item): bool => is_array($item))
        ->map(fn (array $item): array => [...$item, 'url' => Navigation::resolve($item)])
        ->filter(fn (array $item): bool => filled($item['url']) && filled($item['label'] ?? null))
        ->values();

    $networks = collect([
        'instagram' => 'ri-instagram-line',
        'facebook'  => 'ri-facebook-line',
        'youtube'   => 'ri-youtube-line',
        'linkedin'  => 'ri-linkedin-line',
        'twitter'   => 'ri-twitter-x-line',
        'tiktok'    => 'ri-tiktok-line',
    ])
        ->map(fn (string $icon, string $network): array => ['icon' => $icon, 'url' => config('goognet-ui.social.' . $network)])
        /** A refused address drops the network instead of leaving an icon that goes nowhere. */
        ->filter(fn (array $network): bool => filled(SafeUrl::href($network['url'])));

    $contacts = collect([
        ['label' => $company['mail'] ?? null, 'url' => filled($company['mail'] ?? null) ? 'mailto:' . $company['mail'] : null],
        ['label' => $company['phone'] ?? null, 'url' => filled($company['phone'] ?? null) ? 'tel:' . Phone::digits($company['phone']) : null],
    ])->filter(fn (array $contact): bool => filled($contact['label']));

    $privacyUrl = Navigation::privacyUrl();

    /** The slot wins over the config, so a credit that is a logo or a sentence needs no fork. */
    $hasCredit = isset($credit) ? $credit->isNotEmpty() : filled($agency['name'] ?? null);

    /** Dropped when the menu already lists it: the same link twice in one footer. */
    $legal = collect([filled($privacyUrl) ? ['label' => 'Política de privacidade', 'url' => $privacyUrl] : null])
        ->filter()
        ->reject(fn (array $item): bool => $menu->contains(fn (array $link): bool => url($link['url']) === $item['url']))
        ->values();

    /** neutral-500, not 400: at 12px the lighter grey measures 2.6:1 against the 4.5:1 AA asks. */
    $columnTitle = $ui->classes('column-title', 'text-xs font-semibold tracking-[0.08em] text-neutral-500 uppercase');

    /** `py-1` lifts the target from 20px to 28px; WCAG 2.2 asks 24px. */
    $columnLink = $ui->classes('column-link', 'block py-1 text-sm text-neutral-600 transition-colors duration-(--duration-base) ease-(--ease-fluid) hover:text-neutral-900');
@endphp

<footer
    class="{{ ClassList::merge($ui->classes('base', 'border-t border-neutral-200 bg-white'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    @if ($callout)
        <div class="{{ $ui->classes('callout', 'bg-primary-50 border-b border-neutral-200') }}">
            <x-goognet-ui::container class="flex flex-col items-start justify-between gap-5 py-8 sm:flex-row sm:items-center">
                <div>
                    <p class="{{ $ui->classes('callout-title', 'text-xl font-semibold tracking-tight text-neutral-950') }}">
                        {{ $calloutTitle }}
                    </p>

                    @if (filled($calloutText))
                        <p class="{{ $ui->classes('callout-text', 'mt-1.5 text-sm text-neutral-600') }}">
                            {{ $calloutText }}
                        </p>
                    @endif
                </div>

                <x-goognet-ui::button variant="primary" :href="Whatsapp::url()" external class="shrink-0">
                    {{ $calloutAction }}
                </x-goognet-ui::button>
            </x-goognet-ui::container>
        </div>
    @endif

    <x-goognet-ui::container class="py-12">
        <div class="flex flex-col justify-between gap-6 border-b border-neutral-100 pb-8 sm:flex-row sm:items-center">
            <div>
                <x-goognet-ui::brand :class="$ui->classes('brand', 'h-7 w-auto')" />

                @if (filled($description ?? $company['description'] ?? null))
                    <p class="{{ $ui->classes('description', 'mt-3 max-w-md text-sm leading-relaxed text-pretty text-neutral-500') }}">
                        {{ $description ?? $company['description'] }}
                    </p>
                @endif
            </div>

            @if ($networks->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($networks as $network => $social)
                        <x-goognet-ui::link
                            :href="$social['url']"
                            external
                            underline="none"
                            :label="Str::headline($network)"
                            :icon="$social['icon']"
                            :class="$ui->classes('social', 'flex size-11 items-center justify-center rounded-control border border-neutral-200 text-neutral-500 transition-colors duration-(--duration-base) ease-(--ease-fluid) hover:border-neutral-300 hover:text-neutral-900 sm:size-10')"
                        />
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-8 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
            @if ($menu->isNotEmpty())
                <nav aria-label="{{ __('goognet-ui::ui.footer.label') }}">
                    <p class="{{ $columnTitle }}">{{ __('goognet-ui::ui.footer.navigation') }}</p>

                    <div class="mt-3 flex flex-col gap-1">
                        @foreach ($menu as $item)
                            <x-goognet-ui::link :href="$item['url']" underline="none" :class="$columnLink">
                                {{ $item['label'] }}</x-goognet-ui::link>
                        @endforeach
                    </div>
                </nav>
            @endif

            @if ($contacts->isNotEmpty())
                <div>
                    <p class="{{ $columnTitle }}">Contato</p>

                    <div class="mt-3 flex flex-col gap-1">
                        @foreach ($contacts as $contact)
                            <x-goognet-ui::link :href="$contact['url']" underline="none" :class="$columnLink">
                                {{ $contact['label'] }}</x-goognet-ui::link>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($slot->isNotEmpty())
                {{-- A column like the others; anything wider says so with `sm:col-span-2`. --}}
                <div class="{{ $ui->classes('extra', '') }}">{{ $slot }}</div>
            @endif

            @if ($legal->isNotEmpty())
                <div>
                    <p class="{{ $columnTitle }}">Institucional</p>

                    <div class="mt-3 flex flex-col gap-1">
                        @foreach ($legal as $item)
                            <x-goognet-ui::link :href="$item['url']" underline="none" :class="$columnLink">
                                {{ $item['label'] }}</x-goognet-ui::link>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-goognet-ui::container>

    <x-goognet-ui::container>
        <div class="{{ $ui->classes('bottom', 'flex flex-wrap items-center justify-between gap-4 border-t border-neutral-100 py-5 text-sm text-neutral-500') }}">
            <p>&copy; {{ now()->year }} {{ $company['name'] ?? '' }}. {{ __('goognet-ui::ui.footer.rights') }}</p>

            <x-goognet-ui::link
                href="#"
                underline="hover"
                icon-trailing="heroicon-m-arrow-up"
                class="inline-flex items-center gap-1.5 py-1 text-neutral-600"
            >
                {{ __('goognet-ui::ui.footer.top') }}</x-goognet-ui::link>
        </div>

        @if ($validator || $hasCredit)
            {{-- `pb-24` is room for the floating WhatsApp button, below the text rather than beside it. --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-neutral-100 py-5 pb-24 text-sm text-neutral-500">
                @if ($validator)
                    <x-goognet-ui::link
                        :href="'https://validator.w3.org/nu/?doc=' . urlencode(url()->current())"
                        external
                        rel="nofollow noreferrer noopener"
                        underline="none"
                        :class="$ui->classes('validator', 'inline-flex h-9 items-center gap-2 rounded-full border border-neutral-200 px-3.5 text-[13px] font-medium text-neutral-700 transition-colors duration-(--duration-base) ease-(--ease-fluid) hover:border-neutral-300 hover:text-neutral-900')"
                    >
                        <x-ri-html5-fill class="text-primary size-4 shrink-0" />

                        W3C Validator
                    </x-goognet-ui::link>
                @endif

                @if ($hasCredit)
                    <div class="{{ $ui->classes('credit', '') }}">
                        @isset($credit)
                            {{ $credit }}
                        @else
                            <p>
                                {{ __('goognet-ui::ui.footer.credit') }}
                                <x-goognet-ui::link
                                    :href="$agency['url'] ?? null"
                                    external
                                    underline="hover"
                                    class="text-neutral-600"
                                >
                                    {{ $agency['name'] }}</x-goognet-ui::link>
                            </p>
                        @endisset
                    </div>
                @endif
            </div>
        @endif
    </x-goognet-ui::container>
</footer>
