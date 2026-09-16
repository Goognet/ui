@props([
    'policy' => null,
    'name'   => null,
])

@php
    use Goognet\Ui\Support\ConsentCookie;
    use Goognet\Ui\Support\Navigation;
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** Named by the config, which is also what exempts the cookie from encryption. */
    $name = ConsentCookie::name($name);

    $policy = SafeUrl::href($policy ?? Navigation::privacyUrl());
@endphp

@unless (request()->cookie($name) === 'accepted')
    <div
        data-cookie-consent="{{ $name }}"
        role="dialog"
        aria-labelledby="{{ $name }}-title"
        {{
            $attributes->class([
                'fixed inset-x-0 bottom-0 z-50 rounded-t-2xl bg-white p-5',
                'sm:inset-x-auto sm:bottom-5 sm:left-5 sm:w-full sm:max-w-md sm:rounded-2xl',
                'shadow-[0_-4px_24px_rgb(16_24_40_/_0.12)] sm:shadow-lifted',
                'transition-[translate,opacity] duration-(--duration-slow) ease-(--ease-fluid)',
                'starting:translate-y-4 starting:opacity-0 motion-reduce:transition-none',
            ])
        }}
    >
        <div class="flex items-start gap-3">
            <x-ri-cookie-line class="size-6 shrink-0 text-neutral-400" aria-hidden="true" />

            <p id="{{ $name }}-title" class="text-sm leading-relaxed text-pretty text-neutral-700">
                @if ($slot->isEmpty())
                    Usamos apenas
                    <b class="font-semibold">cookies essenciais</b>
                    para o funcionamento do site. Ao continuar navegando, você concorda com a nossa política de
                    privacidade.
                @else
                    {{ $slot }}
                @endif
            </p>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            @if (filled($policy))
                <x-goognet-ui::button :href="$policy" size="sm">Saber mais</x-goognet-ui::button>
            @endif

            <x-goognet-ui::button variant="primary" size="sm" data-cookie-accept>Aceitar</x-goognet-ui::button>
        </div>
    </div>
@endunless
