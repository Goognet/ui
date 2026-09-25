@php
    use Goognet\Ui\Support\Catalogue;
    use Goognet\Ui\Support\ComponentProps;
    use Illuminate\Support\Facades\Blade;

    $propsOf = fn (string $component): array => ComponentProps::of($component);

    $components = Catalogue::entries();

    $tagOf = fn (string $source): string => Catalogue::tag($source);

    /** Set by bin/docs when the page is rendered for GitHub Pages instead of served inside a site. */
    $isPublic = (bool) config('goognet-ui.catalogue.public');

    $version = (string) config('goognet-ui.catalogue.version');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @if ($isPublic)
        <meta
            name="description"
            content="Componentes Blade para Laravel e Tailwind CSS 4, acessíveis e com filtro de URL em todo href e src."
        />
        <title>goognet/ui · Componentes Blade para Laravel</title>
        <link
            rel="icon"
            href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23bef264'/%3E%3Cpath d='M9 16h14M16 9v14' stroke='%23191c17' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E"
        />
    @else
        <meta name="robots" content="noindex, nofollow" />
        <title>Componentes · {{ config('app.name') }}</title>
    @endif

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

            @if ($isPublic)
                @if (filled($version))
                    <span class="rounded-full bg-[var(--doc-accent)] px-2.5 py-1 font-mono text-[11px] font-semibold text-[var(--doc-ink)]">{{ $version }}</span>
                @endif

                <a
                    href="https://github.com/Goognet/ui"
                    class="text-sm font-medium text-[var(--doc-muted)] hover:text-[var(--doc-ink)]"
                >GitHub</a>
            @else
                <span class="rounded-full bg-[var(--doc-accent)] px-2.5 py-1 text-[11px] font-semibold tracking-wide text-[var(--doc-ink)] uppercase">
                    apenas em dev
                </span>
            @endif
        </div>
    </header>

    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-10 px-6 lg:grid-cols-[13rem_minmax(0,1fr)] lg:px-8">
        <aside class="sticky top-14 hidden max-h-[calc(100svh-3.5rem)] flex-col self-start py-10 lg:flex">
            <p class="text-[11px] font-semibold tracking-[0.1em] text-[var(--doc-muted)] uppercase">Componentes</p>

            <nav
                data-sidebar
                aria-label="Componentes"
                class="mt-4 flex min-h-0 [scrollbar-gutter:stable] flex-col gap-0.5 overflow-y-auto overscroll-contain"
            >
                <a
                    href="#personalizacao"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-[var(--doc-ink)] transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)] data-[current]:bg-[var(--doc-surface)]"
                >Personalização</a>

                <a
                    href="#idiomas"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-[var(--doc-ink)] transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)] data-[current]:bg-[var(--doc-surface)]"
                >Idiomas</a>

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

                @if ($isPublic)
                    <div class="mt-6 max-w-2xl overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]">
                        <p class="border-b border-[var(--doc-line)] px-4 py-2.5 font-mono text-[11px] tracking-[0.08em] text-[var(--doc-faint)] uppercase">
                            instalação
                        </p>

                        <pre
                            class="overflow-x-auto px-4 py-3 font-mono text-xs leading-relaxed text-[var(--doc-ink-2)]"
                        ><code>composer require goognet/ui
php artisan goognet-ui:install
npm install swiper fslightbox</code></pre>
                    </div>
                @endif
            </header>

            <section
                id="personalizacao"
                class="scroll-mt-20 border-b border-[var(--doc-line)] py-12"
                aria-labelledby="personalizacao-titulo"
            >
                <header>
                    <h2 id="personalizacao-titulo" class="text-2xl font-semibold tracking-tight">Personalização</h2>

                    <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]">
                        Cada site muda a aparência sem copiar componente nenhum, em três camadas. Nada disso se perde ao
                        atualizar o pacote.
                    </p>
                </header>

                @php
                    $layers = [
                        [
                            'title' => '1. Tokens — a identidade inteira em poucas linhas',
                            'text'  => 'No <code>@theme</code> do site, depois do import do pacote. Todo controle acompanha.',
                            'lang'  => 'css',
                            'code'  => "@theme {\n    --color-primary-500: var(--color-blue-500);\n    --radius-control: 0;\n    --font-weight-control: 700;\n    --spacing-control: 3rem;\n}",
                        ],
                        [
                            'title' => '2. Classe na chamada — sempre vence',
                            'text'  => 'Uma classe passada substitui a do componente para a mesma propriedade, em vez de brigar com ela.',
                            'lang'  => 'blade',
                            'code'  => '<x-ui.button class="rounded-full uppercase">Enviar</x-ui.button>',
                        ],
                        [
                            'title' => '3. Por projeto, em PHP — variantes, tamanhos e partes',
                            'text'  => 'No <code>AppServiceProvider</code> do site. A chamada ainda tem a última palavra sobre isto.',
                            'lang'  => 'php',
                            'code'  => "use Goognet\\Ui\\Ui;\n\nUi::button()\n    ->defaults(['variant' => 'primary', 'rounded' => 'full'])\n    ->variant('outline', 'border-2 border-primary bg-transparent text-primary-ink')\n    ->size('xl', 'h-14 px-8 text-lg')\n    ->part('base', 'uppercase tracking-wide');",
                        ],
                    ];
                @endphp

                @foreach ($layers as $layer)
                    <article
                        class="mt-8 overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]"
                        aria-labelledby="personalizacao-{{ $loop->index }}"
                    >
                        <header class="border-b border-[var(--doc-line)] px-4 py-3">
                            <h3 id="personalizacao-{{ $loop->index }}" class="text-sm font-semibold">
                                {{ $layer['title'] }}
                            </h3>

                            <p class="mt-1 text-sm text-[var(--doc-muted)]">{!! $layer['text'] !!}</p>
                        </header>

                        @if ($loop->first)
                            <div class="flex flex-wrap items-center gap-6 p-6">
                                <div class="flex flex-wrap items-center gap-3">
                                    <x-goognet-ui::button variant="primary">Padrão</x-goognet-ui::button>
                                    <x-goognet-ui::button>Padrão</x-goognet-ui::button>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3"
                                    style="--radius-control: 0; --font-weight-control: 700; --spacing-control: 3rem"
                                >
                                    <x-goognet-ui::button variant="primary">Com tokens</x-goognet-ui::button>
                                    <x-goognet-ui::button>Com tokens</x-goognet-ui::button>
                                </div>
                            </div>
                        @endif

                        <pre class="overflow-x-auto bg-[var(--doc-sunken)] px-4 py-3 font-mono text-xs leading-relaxed text-[var(--doc-ink-2)]"><code>{{ $layer['code'] }}</code></pre>
                    </article>
                @endforeach

                <p class="mt-6 max-w-2xl text-sm leading-relaxed text-[var(--doc-muted)]">
                    Último recurso: <code>php artisan vendor:publish --tag=goognet-ui-views</code> copia as views para o
                    site. A partir daí elas deixam de receber as atualizações do pacote.
                </p>
            </section>

            <section
                id="idiomas"
                class="scroll-mt-20 border-b border-[var(--doc-line)] py-12"
                aria-labelledby="idiomas-titulo"
            >
                <header>
                    <h2 id="idiomas-titulo" class="text-2xl font-semibold tracking-tight">Idiomas</h2>

                    <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]">
                        Todo texto que um componente escreve sozinho — o botão de fechar, o aviso de cookies, o rodapé,
                        a paginação — sai dos arquivos de idioma, e não do Blade. O pacote traz
                        <code>pt_BR</code>, <code>en</code> e <code>es</code>. O que o visitante lê segue o
                        <code>app.locale</code> do request, então um site multi-idioma só precisa chamar
                        <code>App::setLocale()</code>.
                    </p>
                </header>

                <div class="mt-8 grid gap-6 lg:grid-cols-2">
                    <article class="min-w-0 rounded-2xl border border-[var(--doc-line)] bg-[var(--doc-surface)] p-5">
                        <h3 class="text-sm font-semibold">Trocar uma frase</h3>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--doc-muted)]">
                            Crie o arquivo com apenas as chaves que quiser mudar. As que faltarem continuam vindo do
                            pacote, então não há o que manter em dia.
                        </p>

                        <pre
                            class="mt-4 overflow-x-auto rounded-xl bg-[var(--doc-sunken)] px-4 py-3 font-mono text-xs leading-relaxed text-[var(--doc-ink-2)]"
                        ><code>// lang/vendor/goognet-ui/pt_BR/ui.php
return [
    'cookie' =&gt; ['accept' =&gt; 'Tudo bem'],
];</code></pre>
                    </article>

                    <article class="min-w-0 rounded-2xl border border-[var(--doc-line)] bg-[var(--doc-surface)] p-5">
                        <h3 class="text-sm font-semibold">Adicionar um idioma</h3>

                        <p class="mt-2 text-sm leading-relaxed text-[var(--doc-muted)]">
                            Publique os três que existem, use um como molde e traduza. Um idioma sem tradução cai no
                            <code>fallback_locale</code> da aplicação.
                        </p>

                        <pre class="mt-4 overflow-x-auto rounded-xl bg-[var(--doc-sunken)] px-4 py-3 font-mono text-xs leading-relaxed text-[var(--doc-ink-2)]"><code>php artisan vendor:publish --tag=goognet-ui-lang</code></pre>
                    </article>
                </div>

                <p class="mt-6 max-w-2xl text-sm leading-relaxed text-[var(--doc-muted)]">
                    As frases com número ou termo no meio usam marcador — <code>:first</code>, <code>:last</code>,
                    <code>:total</code>, <code>:page</code>, <code>:essential</code> — e não concatenação, para que a
                    ordem das palavras possa mudar de um idioma para outro.
                </p>
            </section>

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
