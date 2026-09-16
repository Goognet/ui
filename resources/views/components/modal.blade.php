@props([
    'name'     => null,
    'title'    => null,
    'size'     => 'base',
    'closable' => true,
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    if (blank($name)) {
        throw new InvalidArgumentException('The modal component requires a name, which is what a trigger points at: name="orcamento".');
    }

    $titleId = $name . '-title';

    $sizes = [
        'sm'   => 'max-w-sm',
        'base' => 'max-w-lg',
        'lg'   => 'max-w-2xl',
        'xl'   => 'max-w-4xl',
        'full' => 'max-w-[calc(100vw-2rem)]',
    ];

    /** A closed dialog is `display: none`; `allow-discrete` and `starting:` are what let it fade. */
    $classes = [
        'm-auto w-full p-0 rounded-xl bg-white shadow-lifted scale-95 opacity-0 transition-all duration-(--duration-base) ease-(--ease-fluid) [transition-behavior:allow-discrete]',
        'open:scale-100 open:opacity-100 starting:open:scale-95 starting:open:opacity-0',
        'backdrop:bg-neutral-950/50 backdrop:opacity-0 backdrop:transition-opacity backdrop:duration-(--duration-base) ease-(--ease-fluid) open:backdrop:opacity-100 starting:open:backdrop:opacity-0',
        $sizes[$size] ?? $sizes['base'],
    ];
@endphp

<dialog
    id="{{ $name }}"
    data-modal
    @if (! $closable) data-modal-static @endif
    @if (filled($title)) aria-labelledby="{{ $titleId }}" @endif
    {{ $attributes->class($classes) }}
>
    {{-- Padding lives inside, so a click on the dialog element itself is a click on the backdrop. --}}
    <div class="flex max-h-[85vh] flex-col">
        @if (filled($title) || $closable)
            <div class="flex items-start justify-between gap-4 border-b border-neutral-200 p-5">
                @if (filled($title))
                    <h2 id="{{ $titleId }}" class="text-lg font-semibold text-neutral-950">{{ $title }}</h2>
                @endif

                @if ($closable)
                    <x-goognet-ui::button
                        variant="ghost"
                        size="sm"
                        square
                        rounded
                        icon="heroicon-o-x-mark"
                        class="ms-auto"
                        data-modal-close
                    >
                        <span class="sr-only">Fechar</span>
                    </x-goognet-ui::button>
                @endif
            </div>
        @endif

        <div class="overflow-y-auto p-5 text-neutral-700">{{ $slot }}</div>

        @if (isset($footer))
            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-neutral-200 p-5">
                {{ $footer }}
            </div>
        @endif
    </div>
</dialog>
