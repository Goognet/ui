@props([
    'policy' => null,
    'name'   => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\ConsentCookie;
    use Goognet\Ui\Support\Navigation;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('cookie-consent');

    /** Named by the config, which is also what exempts the cookie from encryption. */
    $name = ConsentCookie::name($name);

    $policy = SafeUrl::href($policy ?? Navigation::privacyUrl());
@endphp

@unless (request()->cookie($name) === 'accepted')
    <div
        data-cookie-consent="{{ $name }}"
        role="dialog"
        aria-labelledby="{{ $name }}-title"
        class="{{
            ClassList::merge($ui->classes('base', implode(' ', [
                'fixed inset-x-0 bottom-0 z-50 rounded-t-2xl bg-white p-5',
                'sm:inset-x-auto sm:bottom-5 sm:left-5 sm:w-full sm:max-w-md sm:rounded-2xl',
                'shadow-[0_-4px_24px_rgb(16_24_40_/_0.12)] sm:shadow-surface',
                'transition-[translate,opacity] duration-(--duration-slow) ease-(--ease-fluid)',
                'starting:translate-y-4 starting:opacity-0 motion-reduce:transition-none',
            ])), (string) $attributes->get('class'))
        }}"
        {{ $attributes->except('class') }}
    >
        <div class="flex items-start gap-3">
            <x-ri-cookie-line class="size-6 shrink-0 text-neutral-400" aria-hidden="true" />

            <p
                id="{{ $name }}-title"
                class="{{ $ui->classes('text', 'text-sm leading-relaxed text-pretty text-neutral-700') }}"
            >
                @if ($slot->isEmpty())
                    {{--
                        Printed raw because the sentence carries the emphasised term inside it, and
                        word order moves between languages. Both halves come from the translation
                        files and the term is escaped on the way in, so no markup can ride along.
                    --}}
                    {!! __('goognet-ui::ui.cookie.message', ['essential' => '<b class="font-semibold">' . e(__('goognet-ui::ui.cookie.essential')) . '</b>']) !!}
                @else
                    {{ $slot }}
                @endif
            </p>
        </div>

        <div class="{{ $ui->classes('actions', 'mt-4 flex flex-wrap items-center gap-2') }}">
            @if (filled($policy))
                <x-goognet-ui::button
                    :href="$policy"
                    size="sm"
                >{{ __('goognet-ui::ui.cookie.more') }}</x-goognet-ui::button>
            @endif

            <x-goognet-ui::button
                variant="primary"
                size="sm"
                data-cookie-accept
            >{{ __('goognet-ui::ui.cookie.accept') }}</x-goognet-ui::button>
        </div>
    </div>
@endunless
