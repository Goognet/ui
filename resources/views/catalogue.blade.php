@php
    use Goognet\Ui\Support\Catalogue;
    use Goognet\Ui\Support\ComponentProps;
    use Illuminate\Support\Facades\Blade;

    $propsOf = fn (string $component): array => ComponentProps::of($component);

    $components = Catalogue::entries();

    $tagOf = fn (string $source): string => Catalogue::tag($source);
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Componentes · {{ config('app.name') }}</title>

    @vite((array) config('goognet-ui.catalogue.vite'))

    <style>
        :root {
            --doc-bg: #f3f4f1;
            --doc-surface: #ffffff;
            --doc-sunken: #fbfbf9;
            --doc-ink: #191c17;
            --doc-ink-2: #3a3f35;
            --doc-muted: #58624e;
            /* 4.6:1 on the page ground, where #a7b09d measured 2.0:1 — this tone labels the prop tables. */
            --doc-faint: #67715d;
            --doc-line: #e4e7e0;
            --doc-line-2: #ccd1c5;
            --doc-accent: #bef264;
        }
    </style>
</head>
<body class="bg-[var(--doc-bg)] text-[var(--doc-ink)]">
    <header class="sticky top-0 z-20 border-b border-[var(--doc-line)] bg-[var(--doc-surface)]/85 backdrop-blur">
        <div class="mx-auto flex h-14 max-w-7xl items-center gap-4 px-6 lg:px-8">
            {{-- The package's own mark, inline: it ships no asset the site's Vite could serve. The file is part of the package. --}}
            <span
                class="[&>svg]:h-full [&>svg]:w-auto inline-flex h-6 shrink-0"
                role="img"
                aria-label="Goognet"
            >{!! Catalogue::logo() !!}</span>

            <span class="h-5 w-px shrink-0 bg-[var(--doc-line-2)]"></span>

            <span class="truncate text-sm font-medium text-[var(--doc-muted)]">Biblioteca de componentes</span>

            <span class="ms-auto hidden font-mono text-xs text-[var(--doc-faint)] sm:block">
                {{ count($components) }} componentes
            </span>

            <span class="rounded-full bg-[var(--doc-accent)] px-2.5 py-1 text-[11px] font-semibold tracking-wide text-[var(--doc-ink)] uppercase">
                apenas em dev
            </span>
        </div>
    </header>

    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-10 px-6 lg:grid-cols-[13rem_minmax(0,1fr)] lg:px-8">
        <aside class="sticky top-14 hidden max-h-[calc(100svh-3.5rem)] flex-col self-start py-10 lg:flex">
            <p class="text-[11px] font-semibold tracking-[0.1em] text-[var(--doc-muted)] uppercase">Componentes</p>

            <nav
                data-sidebar
                aria-label="Componentes"
                class="mt-4 flex min-h-0 flex-col gap-0.5 overflow-y-auto overscroll-contain [scrollbar-gutter:stable]"
            >
                @foreach ($components as $doc)
                    <a
                        href="#{{ $doc['name'] }}"
                        class="rounded-lg px-3 py-1.5 text-sm text-[var(--doc-muted)] transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)] hover:text-[var(--doc-ink)] data-[current]:bg-[var(--doc-surface)] data-[current]:font-medium data-[current]:text-[var(--doc-ink)]"
                    >{{ $doc['title'] }}</a>
                @endforeach
            </nav>
        </aside>

        <main class="min-w-0 py-10 lg:py-14">
            <header class="border-b border-[var(--doc-line)] pb-8">
                <h1 class="text-3xl font-bold tracking-tight">Componentes de UI</h1>

                <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]">
                    Cada exemplo abaixo é renderizado de verdade, com o mesmo CSS e o mesmo JavaScript do site. As
                    tabelas de props são lidas do código na hora, então não envelhecem.
                </p>
            </header>

            @foreach ($components as $doc)
                <section
                    id="{{ $doc['name'] }}"
                    class="scroll-mt-20 border-b border-[var(--doc-line)] py-12 last:border-0"
                    aria-labelledby="{{ $doc['name'] }}-titulo"
                >
                    <header class="flex flex-wrap items-baseline gap-x-3 gap-y-2">
                        <h2 id="{{ $doc['name'] }}-titulo" class="text-2xl font-semibold tracking-tight">
                            {{ $doc['title'] }}
                        </h2>

                        @foreach ($doc['sources'] as $source)
                            <code class="rounded-md bg-[var(--doc-line)] px-2 py-1 font-mono text-xs text-[var(--doc-muted)]">
                                &lt;x-{{ $tagOf($source) }}&gt;
                            </code>
                        @endforeach
                    </header>

                    {{-- Same trust as the notes below: both come from resources/docs/components.php, which ships with the package. --}}
                    <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]">
                        {!! $doc['description'] !!}
                    </p>

                    @foreach ($doc['sources'] as $source)
                        @php($props = $propsOf($source))

                        @if (filled($props))
                            <div class="mt-8 overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]">
                                <p class="border-b border-[var(--doc-line)] px-4 py-2.5 font-mono text-[11px] font-medium tracking-[0.08em] text-[var(--doc-muted)] uppercase">
                                    x-{{ $tagOf($source) }}
                                </p>

                                <table class="w-full text-left text-sm">
                                    <thead class="text-[11px] tracking-wide text-[var(--doc-faint)] uppercase">
                                        <tr>
                                            <th class="px-4 py-2 font-medium">Prop</th>
                                            <th class="px-4 py-2 font-medium">Padrão</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-[var(--doc-bg)]">
                                        @foreach ($props as $prop)
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach

                    @if (filled($doc['notes'] ?? []))
                        <ul class="mt-8 flex list-none flex-col gap-2.5 border-s-2 border-[var(--doc-accent)] ps-4 text-sm leading-relaxed text-pretty text-[var(--doc-muted)]">
                            @foreach ($doc['notes'] as $note)
                                <li>{!! $note !!}</li>
                            @endforeach
                        </ul>
                    @endif

                    @foreach ($doc['examples'] as $example)
                        @php($exampleId = $doc['name'] . '-exemplo-' . $loop->index)

                        <article
                            class="mt-8 overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]"
                            aria-labelledby="{{ $exampleId }}"
                        >
                            <header class="flex items-center gap-2.5 border-b border-[var(--doc-line)] px-4 py-3">
                                <span class="size-1.5 rounded-full bg-[var(--doc-accent)]" aria-hidden="true"></span>

                                <h3 id="{{ $exampleId }}" class="text-sm font-semibold">{{ $example['title'] }}</h3>
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
                </section>
            @endforeach
        </main>
    </div>

    {{-- Dev-only page, so its one behaviour rides inline instead of entering the site bundle. --}}
    <script>
        document.querySelectorAll('[data-copy]').forEach((button) => {
            button.addEventListener('click', async () => {
                await navigator.clipboard.writeText(button.closest('div').nextElementSibling.textContent);

                button.textContent = 'Copiado';

                setTimeout(() => (button.textContent = 'Copiar'), 1500);
            });
        });
    </script>
</body>
</html>
