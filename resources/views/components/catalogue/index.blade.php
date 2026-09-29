@props(['link'])

@php
    use Goognet\Ui\Support\Catalogue;

    $isPublic = (bool) config('goognet-ui.catalogue.public');

    $entries = collect(Catalogue::entries())->keyBy('name');

    $card = 'overflow-hidden rounded-xl border border-[var(--doc-line)] bg-[var(--doc-surface)]';

    $prose = 'text-[15px] leading-relaxed text-pretty text-[var(--doc-muted)]';

    $layers = [
        [
            'title' => '1. Tokens — a identidade inteira em poucas linhas',
            'text'  => 'No <code>@theme</code> do site, depois do import do pacote. Todo controle acompanha. Cada página de componente lista os tokens que ele lê.',
            'code'  => "@theme {\n    --color-primary-500: var(--color-blue-500);\n    --radius-control: 0;\n    --font-weight-control: 700;\n    --spacing-control: 3rem;\n}",
        ],
        [
            'title' => '2. Classe na chamada — sempre vence',
            'text'  => 'Uma classe passada substitui a do componente para a mesma propriedade, em vez de brigar com ela.',
            'code'  => '<x-ui.button class="rounded-full uppercase">Enviar</x-ui.button>',
        ],
        [
            'title' => '3. Por projeto, em PHP — variantes, tamanhos e partes',
            'text'  => 'No <code>AppServiceProvider</code> do site. A chamada ainda tem a última palavra sobre isto. Cada página lista as partes que o componente expõe.',
            'code'  => "use Goognet\\Ui\\Ui;\n\nUi::button()\n    ->defaults(['variant' => 'primary', 'rounded' => 'full'])\n    ->variant('inverted', 'bg-white text-neutral-900 hover:bg-neutral-100')\n    ->size('xl', 'h-14 px-8 text-lg')\n    ->part('base', 'uppercase tracking-wide');",
        ],
    ];
@endphp

<x-goognet-ui::catalogue.layout :link="$link">
    <header class="border-b border-[var(--doc-line)] pb-8">
        <h1 class="text-3xl font-bold tracking-tight">Componentes de UI</h1>

        <p class="mt-3 max-w-2xl {{ $prose }}">
            Uma página por componente, com os exemplos renderizados de verdade — mesmo CSS e mesmo JavaScript de um
            site. As tabelas de props são lidas do código na hora, então não envelhecem.
        </p>

        @if ($isPublic)
            <div class="mt-6 max-w-2xl {{ $card }}">
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
        class="scroll-mt-20 border-b border-[var(--doc-line)] py-12"
        id="componentes"
        aria-labelledby="componentes-titulo"
    >
        <h2 id="componentes-titulo" class="text-2xl font-semibold tracking-tight">Por onde você começa</h2>

        <p class="mt-3 max-w-2xl {{ $prose }}">
            Agrupados pelo que você veio fazer, não pela ordem do alfabeto: quem está montando um cabeçalho quer os
            quatro componentes de cabeçalho um do lado do outro.
        </p>

        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            @foreach (Catalogue::groups() as $group => $names)
                <section class="{{ $card }}" aria-labelledby="grupo-{{ $loop->index }}">
                    <h3
                        id="grupo-{{ $loop->index }}"
                        class="border-b border-[var(--doc-line)] px-4 py-2.5 text-sm font-semibold"
                    >
                        {{ $group }}
                    </h3>

                    <ul class="flex list-none flex-col divide-y divide-[var(--doc-bg)]">
                        @foreach ($names as $name)
                            <li>
                                <a
                                    href="{{ $link($name) }}"
                                    class="group flex flex-col gap-0.5 px-4 py-2.5 transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-sunken)]"
                                >
                                    <span class="text-sm font-medium group-hover:underline group-hover:decoration-[var(--doc-line-2)] group-hover:underline-offset-4">
                                        {{ $entries[$name]['title'] }}
                                    </span>

                                    <span class="font-mono text-[11px] text-[var(--doc-faint)]">
                                        &lt;x-{{ Catalogue::tag($name) }}&gt;
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    </section>

    <section
        class="scroll-mt-20 border-b border-[var(--doc-line)] py-12"
        id="personalizacao"
        aria-labelledby="personalizacao-titulo"
    >
        <h2 id="personalizacao-titulo" class="text-2xl font-semibold tracking-tight">Personalização</h2>

        <p class="mt-3 max-w-2xl {{ $prose }}">
            Cada site muda a aparência sem copiar componente nenhum, em três camadas. Nada disso se perde ao atualizar o
            pacote.
        </p>

        @foreach ($layers as $layer)
            <article class="mt-8 {{ $card }}" aria-labelledby="personalizacao-{{ $loop->index }}">
                <header class="border-b border-[var(--doc-line)] px-4 py-3">
                    <h3 id="personalizacao-{{ $loop->index }}" class="text-sm font-semibold">{{ $layer['title'] }}</h3>

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
            Último recurso: <code>php artisan vendor:publish --tag=goognet-ui-views</code> copia as views para o site. A
            partir daí elas deixam de receber as atualizações do pacote.
        </p>
    </section>

    <section class="scroll-mt-20 py-12" id="idiomas" aria-labelledby="idiomas-titulo">
        <h2 id="idiomas-titulo" class="text-2xl font-semibold tracking-tight">Idiomas</h2>

        <p class="mt-3 max-w-2xl {{ $prose }}">
            Todo texto que um componente escreve sozinho — o botão de fechar, o aviso de cookies, o rodapé, a paginação
            — sai dos arquivos de idioma, e não do Blade. O pacote traz
            <code>pt_BR</code>, <code>en</code> e <code>es</code>. O que o visitante lê segue o
            <code>app.locale</code> do request, então um site multi-idioma só precisa chamar
            <code>App::setLocale()</code>.
        </p>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <article class="min-w-0 rounded-2xl border border-[var(--doc-line)] bg-[var(--doc-surface)] p-5">
                <h3 class="text-sm font-semibold">Trocar uma frase</h3>

                <p class="mt-2 text-sm leading-relaxed text-[var(--doc-muted)]">
                    Crie o arquivo com apenas as chaves que quiser mudar. As que faltarem continuam vindo do pacote,
                    então não há o que manter em dia.
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
            <code>:total</code>, <code>:page</code>, <code>:essential</code> — e não concatenação, para que a ordem das
            palavras possa mudar de um idioma para outro.
        </p>
    </section>
</x-goognet-ui::catalogue.layout>
