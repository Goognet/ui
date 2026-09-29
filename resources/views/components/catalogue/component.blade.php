@props([
    /** @var array<string, mixed> */
    'doc',

    'link',
])

@php
    use Goognet\Ui\Support\Catalogue;
    use Goognet\Ui\Support\ComponentProps;
    use Illuminate\Support\Facades\Blade;

    $card = 'overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]';

    $cardLabel = 'border-b border-[var(--doc-line)] px-4 py-2.5 font-mono text-[11px] font-medium tracking-[0.08em] text-[var(--doc-muted)] uppercase';

    $prose = 'text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]';

    $titleOf = collect(Catalogue::entries())->pluck('title', 'name');

    /** The rest of this component's group, for the narrow-screen navigation at the foot of the page. */
    $group = collect(Catalogue::groups())->first(fn (array $names): bool => in_array($doc['name'], $names, true)) ?? [];

    $siblings = array_values(array_diff($group, [$doc['name']]));
@endphp

<x-goognet-ui::catalogue.layout :heading="$doc['title']" :link="$link" :current="$doc['name']">
    <header class="border-b border-[var(--doc-line)] pb-8">
        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-2">
            <h1 class="text-3xl font-bold tracking-tight">{{ $doc['title'] }}</h1>

            @foreach ($doc['sources'] as $source)
                <code class="rounded-md bg-[var(--doc-line)] px-2 py-1 font-mono text-xs text-[var(--doc-muted)]">
                    &lt;x-{{ Catalogue::tag($source) }}&gt;
                </code>
            @endforeach
        </div>

        {{-- Trusted like the notes: both come from resources/docs/components.php, which ships with the package. --}}
        <p class="mt-3 max-w-2xl {{ $prose }}">{!! $doc['description'] !!}</p>
    </header>

    @foreach ($doc['examples'] as $example)
        @php($exampleId = $doc['name'] . '-exemplo-' . $loop->index)

        <article class="mt-8 {{ $card }}" aria-labelledby="{{ $exampleId }}">
            <header class="flex items-center gap-2.5 border-b border-[var(--doc-line)] px-4 py-3">
                <span class="size-1.5 rounded-full bg-[var(--doc-accent)]" aria-hidden="true"></span>

                <h2 id="{{ $exampleId }}" class="text-sm font-semibold">{{ $example['title'] }}</h2>
            </header>

            @php($isRow = ($example['layout'] ?? 'block') === 'row')

            @if ($example['render'] ?? true)
                <div @class(['p-6', 'flex flex-wrap items-center gap-3' => $isRow, '[&>*+*]:mt-3' => ! $isRow])>
                    {!! Blade::render(trim(Catalogue::code($example['code']))) !!}
                </div>
            @else
                <p class="flex items-center gap-2 p-6 text-sm text-[var(--doc-faint)]">
                    {{ svg('heroicon-m-information-circle', 'size-4 shrink-0') }}

                    {{ $example['note'] ?? 'Sem prévia: este exemplo depende de um arquivo que não vive no repositório.' }}
                </p>
            @endif

            <div class="border-t border-[var(--doc-line)] bg-[var(--doc-sunken)]">
                <div class="flex items-center justify-between gap-4 px-4 py-2">
                    <span class="font-mono text-[11px] tracking-[0.06em] text-[var(--doc-faint)] uppercase">blade</span>

                    <button
                        type="button"
                        data-copy
                        class="rounded-md border border-[var(--doc-line-2)] px-2.5 py-1 text-[11px] font-medium text-[var(--doc-muted)] transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)]"
                    >
                        Copiar
                    </button>
                </div>

                <pre class="overflow-x-auto px-4 pb-4 font-mono text-xs leading-relaxed text-[var(--doc-ink-2)]"><code>{{ trim(Catalogue::code($example['code'])) }}</code></pre>
            </div>
        </article>
    @endforeach

    <section class="mt-14 scroll-mt-20" id="props" aria-labelledby="props-titulo">
        <h2 id="props-titulo" class="text-2xl font-semibold tracking-tight">Props</h2>

        @foreach ($doc['sources'] as $source)
            @php($props = ComponentProps::of($source))
            @php($options = ComponentProps::options($source))

            @if (filled($props))
                <div class="mt-6 {{ $card }}">
                    <p class="{{ $cardLabel }}">x-{{ Catalogue::tag($source) }}</p>

                    {{--
                        The card clips, so on a narrow screen a third column would be cut off with
                        no way to reach it. This scrolls instead: nothing is hidden, and a table
                        that fits is unaffected.
                    --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-md text-left text-sm">
                            <thead class="text-[11px] tracking-wide text-[var(--doc-faint)] uppercase">
                                <tr>
                                    <th class="px-4 py-2 font-medium">Prop</th>
                                    <th class="px-4 py-2 font-medium">Padrão</th>
                                    <th class="px-4 py-2 font-medium">Aceita</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[var(--doc-bg)]">
                                @foreach ($props as $prop)
                                    @php($names = match ($prop['name']) {
                                    'variant' => $options['variants'],
                                    'size' => $options['sizes'],
                                    default => [],
                                })

                                    <tr>
                                        <td class="px-4 py-2.5 font-mono text-[var(--doc-ink)]">
                                            {{ $prop['name'] }}

                                            @if ($prop['aware'])
                                                <span class="ms-1 rounded bg-[var(--doc-line)] px-1.5 py-0.5 font-sans text-[11px] text-[var(--doc-muted)]">herdado do pai</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2.5 font-mono text-[var(--doc-muted)]">
                                            {{ $prop['default'] ?? '—' }}

                                            @unless (filled($prop['default']))
                                                <span class="ms-1 rounded bg-amber-100 px-1.5 py-0.5 font-sans text-[11px] font-medium text-amber-800">obrigatório</span>
                                            @endunless
                                        </td>

                                        {{--
                                        The values, not just the type. A table saying `variant` takes a
                                        string sends the reader to the source, and an unknown name falls
                                        back to the default without a word on screen to work it out from.
                                    --}}
                                        <td class="px-4 py-2.5 font-mono text-xs text-[var(--doc-muted)]">
                                            {{ $names === [] ? '' : implode('  ', $names) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach
    </section>

    <section class="mt-14 scroll-mt-20" id="personalizar" aria-labelledby="personalizar-titulo">
        <h2 id="personalizar-titulo" class="text-2xl font-semibold tracking-tight">Personalizar</h2>

        <p class="mt-3 max-w-2xl {{ $prose }}">
            As três camadas estão explicadas em
            <a
                href="{{ $link(null) }}#personalizacao"
                class="font-medium text-[var(--doc-ink)] underline decoration-[var(--doc-line-2)] underline-offset-4"
                >Começar aqui</a
            >. Abaixo, o que <strong>este</strong> componente expõe a cada uma.
        </p>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            @foreach ($doc['sources'] as $source)
                @php($parts = ComponentProps::parts($source))
                @php($tokens = ComponentProps::tokens($source))

                <article class="{{ $card }} lg:col-span-2">
                    <p class="{{ $cardLabel }}">Ui::{{ Illuminate\Support\Str::camel($source) }}()</p>

                    <div class="grid gap-x-8 gap-y-5 p-5 sm:grid-cols-2">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold">Partes</h3>

                            <p class="mt-1 text-sm leading-relaxed text-[var(--doc-muted)]">
                                @if ($parts === [])
                                    Nenhuma: este componente não passa classes por parte.
                                @else
                                    <code>part()</code>
                                    e
                                    <code>replacePart()</code>
                                    aceitam:
                                    {!! collect($parts)->map(fn (string $part): string => '<code>' . $part . '</code>')->join(', ', ' e ') !!}.
                                @endif
                            </p>
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold">Tokens que ele lê</h3>

                            <p class="mt-1 text-sm leading-relaxed text-[var(--doc-muted)]">
                                @if ($tokens === [])
                                    Nenhum específico: ele usa só cor e espaçamento do Tailwind.
                                @else
                                    Redefina no
                                    <code>@theme</code>
                                    do site:
                                    {!! collect($tokens)->map(fn (string $token): string => '<code>' . $token . '</code>')->join(', ', ' e ') !!}.
                                @endif
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if (filled($doc['notes'] ?? []))
        {{--
            Closed by default, and last. These are the decisions behind the component — why a
            measurement came out the way it did, which browser bug a guard exists for. They are
            the bulk of the page and they are maintainer reading: someone who came to use the
            component should not have to scroll past them to reach the props.
        --}}
        <details class="group mt-14 {{ $card }}">
            <summary class="[&::-webkit-details-marker]:hidden flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-semibold">
                {{ svg('heroicon-m-chevron-right', 'size-4 shrink-0 text-[var(--doc-muted)] transition-transform duration-(--duration-fast) group-open:rotate-90') }}

                Por que é assim

                <span class="font-mono text-[11px] font-normal text-[var(--doc-faint)]">{{ count($doc['notes']) }} notas</span>
            </summary>

            <ul class="flex list-none flex-col gap-2.5 border-t border-[var(--doc-line)] px-4 py-4 text-sm leading-relaxed text-pretty text-[var(--doc-muted)]">
                @foreach ($doc['notes'] as $note)
                    <li class="border-s-2 border-[var(--doc-accent)] ps-3">{!! $note !!}</li>
                @endforeach
            </ul>
        </details>
    @endif

    {{--
        The rail is `lg:` and up, and every component is its own page now: below that width a
        reader who arrived here has no way to any other component. This is that way, and it
        needs no JavaScript and no second menu on a wide screen to maintain.
    --}}
    <nav class="mt-14 border-t border-[var(--doc-line)] pt-8 lg:hidden" aria-label="Componentes próximos">
        @if ($siblings !== [])
            <p class="text-[11px] font-semibold tracking-[0.1em] text-[var(--doc-faint)] uppercase">Do mesmo grupo</p>

            <ul class="mt-3 flex list-none flex-wrap gap-2">
                @foreach ($siblings as $name)
                    <li>
                        <a
                            href="{{ $link($name) }}"
                            class="inline-flex rounded-lg border border-[var(--doc-line)] bg-[var(--doc-surface)] px-3 py-1.5 text-sm text-[var(--doc-muted)]"
                        >{{ $titleOf[$name] ?? $name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif

        <a
            href="{{ $link(null) }}#componentes"
            class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-[var(--doc-ink)] underline decoration-[var(--doc-line-2)] underline-offset-4"
        >
            {{ svg('heroicon-m-squares-2x2', 'size-4') }} Todos os componentes
        </a>
    </nav>
</x-goognet-ui::catalogue.layout>
