<?php

declare(strict_types = 1);

/**
 * Catalogue behind the component gallery at `/dev/components`.
 *
 * Props are not listed here: the page reads them from each component's `@props`
 * block, so the table cannot drift from the code. What lives here is the part a
 * machine cannot infer — what the component is for, how to reach for it, and the
 * traps worth knowing.
 *
 * @return array<int, array{name: string, title: string, description: string, sources: array<int, string>, examples: array<int, array{title: string, code: string}>, notes?: array<int, string>}>
 */
return [
    [
        'name'        => 'button',
        'title'       => 'Button',
        'description' => 'Botão de ação. Vira <code>&lt;a&gt;</code> sozinho quando recebe <code>href</code>, então serve também para call-to-action que navega.',
        'sources'     => ['button'],
        'examples'    => [
            [
                'title'  => 'Variantes',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button>Padrão</x-ui.button>
                    <x-ui.button variant="primary">Primary</x-ui.button>
                    <x-ui.button variant="secondary">Secondary</x-ui.button>
                    <x-ui.button variant="filled">Filled</x-ui.button>
                    <x-ui.button variant="ghost">Ghost</x-ui.button>
                    BLADE,
            ],
            [
                'title'  => 'Tamanhos e raio',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button size="xs">xs</x-ui.button>
                    <x-ui.button size="sm">sm</x-ui.button>
                    <x-ui.button size="base">base</x-ui.button>
                    <x-ui.button size="lg">lg</x-ui.button>
                    <x-ui.button rounded>rounded</x-ui.button>
                    <x-ui.button rounded="lg">rounded="lg"</x-ui.button>
                    BLADE,
            ],
            [
                'title'  => 'Ícone, quadrado, link e estados',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button icon="heroicon-m-paper-airplane">Enviar</x-ui.button>
                    <x-ui.button icon-trailing="heroicon-m-arrow-right">Continuar</x-ui.button>
                    <x-ui.button square icon="heroicon-o-trash" variant="ghost">
                        <span class="sr-only">Excluir</span>
                    </x-ui.button>
                    <x-ui.button href="/orcamento" variant="primary">Vira uma âncora</x-ui.button>
                    <x-ui.button loading>Carregando</x-ui.button>
                    <x-ui.button disabled>Desabilitado</x-ui.button>
                    BLADE,
            ],
            [
                'title'  => 'Link externo e tipo de submit',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button href="https://goognet.com.br" external>Abre em nova aba</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Enviar formulário</x-ui.button>
                    <x-ui.button type="reset" variant="ghost">Limpar</x-ui.button>
                    BLADE,
            ],
        ],
        'notes' => [
            'Altura, arredondamento, peso da fonte e sombra vêm de tokens (<code>--spacing-control</code>, <code>--radius-control</code>, <code>--font-weight-control</code>, <code>--shadow-control</code>). Redefina no <code>@theme</code> do site e todos os botões acompanham — veja a seção <strong>Personalização</strong>, no topo.',
            'Uma classe na chamada substitui a do componente para a mesma propriedade: <code>class="rounded-full h-14"</code> tira o <code>rounded-control</code> e o <code>h-control</code> em vez de somar a eles.',
            '<code>Ui::button()</code> define padrões (<code>defaults</code>), variantes e tamanhos novos, e classes por parte: <code>base</code>, <code>content</code>, <code>icon</code> e <code>spinner</code>. Um tamanho novo vale para o botão comum; o <code>square</code> segue a escala de tokens.',
            'As variantes <code>primary</code> e <code>secondary</code> usam <code>--color-primary</code> e <code>--color-secondary</code> — a cor da marca, sem tom numerado. A tinta por cima é escura: branco sobre o roxo mede 4,12:1 e reprova, <code>neutral-950</code> mede 4,89:1 e passa, então o preenchimento continua sendo a cor que o site escolheu.',
            'Para <em>texto</em> sobre fundo claro existe <code>text-primary-ink</code>: a mesma cor numa luminosidade legível, derivada com <code>oklch(from var(--color-primary) 0.45 c h)</code>. A cor da marca como texto mede 1,95:1 — passar o mouse num link deixava ele menos legível do que estava.',
            'O <code>ink</code> é derivado, não escolhido: ele acompanha qualquer cor que o site defina. Medido em seis marcas bem diferentes, incluindo amarelo (1,57 → 7,43) e ciano (1,81 → 6,33).',
            'Com <code>href</code> o elemento é <code>&lt;a&gt;</code>; sem, é <code>&lt;button&gt;</code> e o prop <code>type</code> passa a valer.',
            '<code>loading</code> e <code>disabled</code> desligam o clique nos dois casos e marcam <code>aria-disabled</code>.',
        ],
    ],
    [
        'name'        => 'badge',
        'title'       => 'Badge',
        'description' => 'Etiqueta curta para estado, categoria ou contagem. Mesmos nomes de variante do <code>x-ui.button</code>; cor semântica sai por classe Tailwind no ponto de uso.',
        'sources'     => ['badge'],
        'examples'    => [
            [
                'title'  => 'Variantes',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.badge>Padrão</x-ui.badge>
                    <x-ui.badge variant="primary">Primary</x-ui.badge>
                    <x-ui.badge variant="secondary">Secondary</x-ui.badge>
                    <x-ui.badge variant="filled">Filled</x-ui.badge>
                    <x-ui.badge variant="ghost">Ghost</x-ui.badge>
                    BLADE,
            ],
            [
                'title'  => 'Cor semântica por classe',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.badge class="bg-green-100 text-green-800" dot>Pago</x-ui.badge>
                    <x-ui.badge class="bg-amber-100 text-amber-800" dot>Pendente</x-ui.badge>
                    <x-ui.badge class="bg-red-100 text-red-800" dot>Atrasado</x-ui.badge>
                    <x-ui.badge class="border-2 border-neutral-300 bg-transparent text-neutral-700">Rascunho</x-ui.badge>
                    BLADE,
            ],
            [
                'title'  => 'Tamanhos, raio, ícone e link',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.badge size="xs">xs</x-ui.badge>
                    <x-ui.badge size="sm">sm</x-ui.badge>
                    <x-ui.badge size="base">base</x-ui.badge>
                    <x-ui.badge size="lg">lg</x-ui.badge>
                    <x-ui.badge rounded="md" variant="filled">rounded="md"</x-ui.badge>
                    <x-ui.badge variant="filled" icon="heroicon-m-check">Concluído</x-ui.badge>
                    <x-ui.badge href="/tags/novo" variant="primary" icon-trailing="heroicon-m-arrow-right">Novo</x-ui.badge>
                    BLADE,
            ],
            [
                'title'  => 'Etiqueta que abre em nova aba',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.badge href="https://goognet.com.br" external variant="filled">Parceiro</x-ui.badge>
                    BLADE,
            ],
        ],
        'notes' => [
            'Sem <code>href</code> é <code>&lt;span&gt;</code>; com, é <code>&lt;a&gt;</code> e ganha <code>focus-visible</code>. <code>external</code> sem <code>href</code> não emite <code>target</code>.',
            'Uma classe passada <strong>substitui</strong> a do variant em vez de somar: duas utilidades de fundo no mesmo elemento são decididas pela ordem na folha de estilo, não pela ordem em que foram escritas. Vale para <code>bg-</code>, <code>text-</code> e <code>border-</code>, cada um por conta própria — <code>class="bg-red-100"</code> troca só o fundo e mantém o texto do variant.',
            'Por isso não existe variante <code>success</code>/<code>warning</code>/<code>danger</code>: a cor semântica vem da paleta do Tailwind no ponto de uso, como no resto da lib.',
            '<code>dot</code> usa <code>bg-current</code>, então o ponto acompanha a cor do texto, inclusive a sobrescrita.',
            '<code>rounded</code> aceita um apelido (<code>sm</code>, <code>md</code>, <code>base</code>, <code>lg</code>, <code>xl</code>, <code>full</code>) ou uma utilidade inteira (<code>rounded-none</code>). Valor que não é nem um nem outro volta para a pílula, em vez de emitir uma classe que não estiliza nada.',
        ],
    ],
    [
        'name'        => 'table',
        'title'       => 'Table',
        'description' => 'Tabela de dados. Rola sozinha quando não cabe, em vez de empurrar a página para o lado.',
        'sources'     => ['table'],
        'examples'    => [
            [
                'title' => 'Cabeçalhos, alinhamento e zebra',
                'code'  => <<<'BLADE'
                    <x-ui.table
                        caption="Planos e preços"
                        striped
                        size="base"
                        :headers="['Plano', 'Inclui', ['label' => 'Preço', 'align' => 'end']]"
                        :rows="[
                            ['Lite', 'Site institucional', 'R$ 90/mês'],
                            ['Pro', 'Site + blog + suporte', 'R$ 190/mês'],
                            ['Sob medida', 'Escopo fechado a cada projeto', 'sob consulta'],
                        ]"
                    />
                    BLADE,
            ],
            [
                'title' => 'Linhas vindas do banco, lidas por chave',
                'code'  => <<<'BLADE'
                    <x-ui.table
                        size="sm"
                        :headers="[
                            ['key' => 'cidade', 'label' => 'Cidade'],
                            ['key' => 'prazo', 'label' => 'Prazo', 'align' => 'end'],
                        ]"
                        :rows="[
                            ['cidade' => 'São Paulo', 'prazo' => '2 dias'],
                            ['cidade' => 'Campinas', 'prazo' => '3 dias'],
                        ]"
                    />
                    BLADE,
            ],
            [
                'title' => 'Escrita à mão',
                'code'  => <<<'BLADE'
                    <x-ui.table :headers="['Serviço', 'Situação']">
                        <tr>
                            <td class="px-4 py-3 text-sm">Consultoria</td>
                            <td class="px-4 py-3 text-sm"><x-ui.badge>Ativo</x-ui.badge></td>
                        </tr>
                    </x-ui.table>
                    BLADE,
            ],
        ],
        'notes' => [
            'A rolagem horizontal fica no invólucro da tabela, não na página: é a única exceção à regra de nunca deixar o corpo rolar para o lado.',
            '<code>headers</code> aceita <code>[\'Plano\']</code>, <code>[\'plano\' => \'Plano\']</code> ou linhas com <code>label</code>, <code>key</code> e <code>align</code>. Com <code>key</code>, cada linha é lida por chave — uma coleção do banco entra sem mapear antes; sem <code>key</code>, é lida por posição.',
            'O alinhamento é declarado uma vez, no cabeçalho, e vale para as células daquela coluna. Preço alinhado à direita com o cabeçalho à esquerda é o erro que isso evita.',
            'Sem <code>rows</code>, o slot é usado como corpo — para quando uma célula precisa de markup, um badge ou um link.',
            '<code>caption</code> vira <code>&lt;caption&gt;</code> de verdade: é o que um leitor de tela anuncia antes de entrar na tabela.',
        ],
    ],
    [
        'name'        => 'tooltip',
        'title'       => 'Tooltip',
        'description' => 'Explicação curta presa a um gatilho. Sem JavaScript: abre no hover e no foco do teclado.',
        'sources'     => ['tooltip'],
        'examples'    => [
            [
                'title'  => 'Em volta de um controle',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.tooltip text="Copia o link desta página">
                        <x-ui.button size="sm" icon="heroicon-m-link">Copiar link</x-ui.button>
                    </x-ui.tooltip>

                    <x-ui.tooltip text="Abre no WhatsApp" placement="bottom">
                        <x-ui.button size="sm" variant="primary">Falar agora</x-ui.button>
                    </x-ui.tooltip>
                    BLADE,
            ],
            [
                'title'  => 'Em texto, que não recebe foco sozinho',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.text>
                        Prazo de
                        <x-ui.tooltip text="Dias úteis, contados a partir da aprovação da arte." focusable placement="top">
                            <x-ui.text inline class="underline decoration-dotted">5 dias</x-ui.text>
                        </x-ui.tooltip>
                    </x-ui.text>

                    <x-ui.tooltip text="À esquerda" placement="left"><x-ui.badge>left</x-ui.badge></x-ui.tooltip>
                    <x-ui.tooltip text="À direita" placement="right"><x-ui.badge>right</x-ui.badge></x-ui.tooltip>
                    BLADE,
            ],
        ],
        'notes' => [
            'Abre no <code>hover</code> e no <code>focus-within</code>: um gatilho alcançado pelo teclado nunca recebe ponteiro, e uma dica que só o mouse abre é uma dica que metade dos visitantes não vê.',
            'Quando o gatilho já é um botão ou um link, não é preciso mais nada. Em texto comum, <code>focusable</code> põe <code>tabindex="0"</code> e <code>aria-describedby</code> no invólucro, que é o que leva a dica ao teclado e ao leitor de tela.',
            'Sem <code>text</code>, o componente rende só o gatilho — nada de bolha vazia numa página gerada por laço.',
            'A bolha tem <code>pointer-events-none</code>: ela nunca fica entre o ponteiro e o que está embaixo.',
        ],
    ],
    [
        'name'        => 'counter',
        'title'       => 'Counter',
        'description' => 'Número que conta até o valor quando entra na tela. O valor final é escrito pelo servidor, então a página sem JavaScript mostra o número certo.',
        'sources'     => ['counter'],
        'examples'    => [
            [
                'title'  => 'Indicadores',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <div class="text-center">
                        <x-ui.counter :value="1250" suffix="+" />
                        <x-ui.text size="sm" class="mt-1 text-neutral-500">projetos entregues</x-ui.text>
                    </div>

                    <div class="text-center">
                        <x-ui.counter :value="98.5" :decimals="1" suffix="%" size="lg" />
                        <x-ui.text size="sm" class="mt-1 text-neutral-500">satisfação</x-ui.text>
                    </div>

                    <div class="text-center">
                        <x-ui.counter :value="12" :start="0" :duration="3" size="sm" />
                        <x-ui.text size="sm" class="mt-1 text-neutral-500">anos de casa</x-ui.text>
                    </div>
                    BLADE,
            ],
            [
                'title'  => 'Moeda e separadores',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.counter :value="1234567.89" :decimals="2" prefix="R$ " separator="." decimal="," size="xl" />
                    BLADE,
            ],
        ],
        'notes' => [
            'O número final é renderizado no servidor e o script conta a partir dele — sem JavaScript, e para um rastreador, a página mostra a figura real em vez de um zero esperando um script que nunca roda.',
            'A animação começa quando o elemento entra na tela, uma vez só: contador que reinicia a cada rolagem lê como defeito.',
            'Quem pede menos movimento no sistema (<code>prefers-reduced-motion</code>) recebe o número, sem contagem.',
            'A biblioteca (<code>countup.js</code>) é importada só quando existe contador na página. Instale com <code>npm install countup.js</code> — o <code>goognet-ui:install</code> avisa quando falta.',
            '<code>decimals</code> é limitado a 4: mais casas viram ruído, e o servidor e o script precisam concordar na formatação.',
        ],
    ],
    [
        'name'        => 'card',
        'title'       => 'Card',
        'description' => 'Bloco de conteúdo sobre uma superfície. Vira <code>&lt;a&gt;</code> sozinho quando recebe <code>href</code>, e só então ganha o movimento de hover.',
        'sources'     => ['card'],
        'examples'    => [
            [
                'title'  => 'Variantes',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.card class="max-w-xs">Padrão, com borda.</x-ui.card>
                    <x-ui.card variant="elevated" class="max-w-xs">Elevated, com sombra.</x-ui.card>
                    <x-ui.card variant="filled" class="max-w-xs">Filled, sem borda.</x-ui.card>
                    <x-ui.card variant="ghost" class="max-w-xs">Ghost, só o espaçamento.</x-ui.card>
                    BLADE,
            ],
            [
                'title' => 'Cabeçalho, rodapé e mídia',
                'code'  => <<<'BLADE'
                    <x-ui.card href="/servicos/consultoria" padding="base" class="max-w-sm">
                        <x-slot:media>
                            <x-ui.image src="https://picsum.photos/seed/card/800/450" alt="" class="aspect-video w-full object-cover" />
                        </x-slot:media>

                        <x-slot:header>
                            <x-ui.heading :level="3" size="sm">Consultoria tributária</x-ui.heading>
                        </x-slot:header>

                        <x-ui.text size="sm">Revisão de regime e recuperação de créditos.</x-ui.text>

                        <x-slot:footer>
                            <x-ui.text size="sm" class="text-neutral-500">Saiba mais</x-ui.text>
                        </x-slot:footer>
                    </x-ui.card>
                    BLADE,
            ],
            [
                'title'  => 'Espaçamento',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.card padding="none" class="max-w-[10rem]">none</x-ui.card>
                    <x-ui.card padding="sm" class="max-w-[10rem]">sm</x-ui.card>
                    <x-ui.card padding="lg" class="max-w-[10rem]">lg</x-ui.card>
                    <x-ui.card href="https://goognet.com.br" external class="max-w-[10rem]">external</x-ui.card>
                    BLADE,
            ],
        ],
        'notes' => [
            'O movimento de hover só existe quando o card leva a algum lugar: movimento promete clique.',
            'A mídia é puxada para fora do espaçamento com margem negativa do tamanho do <code>padding</code>, então a imagem encosta na borda e acompanha o raio do topo.',
            'Um <code>href</code> recusado pelo filtro de URL deixa o card como <code>&lt;div&gt;</code> — sem <code>target</code> nem <code>rel</code> sobrando, que seriam erro de validação.',
        ],
    ],
    [
        'name'        => 'toast',
        'title'       => 'Toast',
        'description' => 'Aviso curto que aparece depois de uma ação e some sozinho. Lê o que a requisição anterior deixou na sessão, então um formulário que deu certo não precisa de mais nada.',
        'sources'     => ['toast'],
        'examples'    => [
            [
                'title' => 'No layout, uma vez',
                'code'  => <<<'BLADE'
                    {{-- No <x-layouts.guest>, junto do rodapé: --}}
                    <x-ui.toast />

                    {{-- E no controller: --}}
                    {{-- return back()->with('success', 'Mensagem enviada. Respondemos no mesmo dia útil.'); --}}
                    BLADE,
            ],
            [
                'title' => 'Direto, sem passar pela sessão',
                'code'  => <<<'BLADE'
                    <x-ui.toast message="Orçamento salvo." title="Pronto" type="success" position="top-end" :duration="4000" />
                    <x-ui.toast message="Não foi possível enviar agora." type="error" session="meu-aviso" />
                    BLADE,
            ],
            [
                'title' => 'Confirmar antes de agir',
                'code'  => <<<'BLADE'
                    <x-ui.button
                        variant="ghost"
                        icon="heroicon-m-trash"
                        data-confirm="Esta ação não pode ser desfeita."
                        data-confirm-title="Excluir o orçamento?"
                        data-confirm-action="Excluir"
                    >
                        Excluir
                    </x-ui.button>
                    BLADE,
            ],
        ],
        'notes' => [
            'Sem mensagem nenhuma, o componente não rende nada — pode ficar no layout o tempo todo.',
            'As chaves lidas da sessão, nesta ordem: <code>toast</code>, <code>success</code>, <code>status</code>, <code>error</code>, <code>warning</code>, <code>info</code>. Cada nome já traz o ícone que promete; <code>session="minha-chave"</code> lê só a sua, sem supor ícone.',
            'O flash pode ser uma string ou um array com <code>message</code>, <code>title</code> e <code>type</code>.',
            'A mensagem vai como texto para a caixa, nunca como HTML: um erro de validação ou um valor vindo do banco não vira markup na tela.',
            '<code>data-confirm</code> em qualquer botão ou link pede confirmação antes de agir — o clique é segurado, a caixa responde, e só então a ação original acontece (formulário é enviado, o resto é clicado de novo).',
            'A biblioteca (<code>sweetalert2</code>) é importada no primeiro uso: página sem toast e sem confirmação não paga por ela.',
        ],
    ],
    [
        'name'        => 'alert',
        'title'       => 'Alert',
        'description' => 'Recado na página: confirmação, erro de formulário, aviso de manutenção. Neutro por padrão, colorido no ponto de uso.',
        'sources'     => ['alert'],
        'examples'    => [
            [
                'title' => 'Cor no ponto de uso',
                'code'  => <<<'BLADE'
                    <x-ui.alert icon="heroicon-m-check-circle" title="Mensagem enviada" live class="border-green-200 bg-green-50 text-green-800">
                        Respondemos no mesmo dia útil.
                    </x-ui.alert>

                    <x-ui.alert icon="heroicon-m-exclamation-triangle" class="border-amber-200 bg-amber-50 text-amber-900">
                        Revise os campos marcados antes de enviar.
                    </x-ui.alert>

                    <x-ui.alert icon="heroicon-m-information-circle" variant="filled" dismissible>
                        Atendimento em horário reduzido nesta sexta.
                    </x-ui.alert>
                    BLADE,
            ],
            [
                'title' => 'Variantes',
                'code'  => <<<'BLADE'
                    <x-ui.alert>Padrão, com borda.</x-ui.alert>
                    <x-ui.alert variant="ghost">Ghost, sem fundo.</x-ui.alert>
                    BLADE,
            ],
        ],
        'notes' => [
            'Não existe variante <code>success</code>/<code>danger</code> aqui pelo mesmo motivo do badge: a cor semântica vem da paleta do Tailwind no ponto de uso, e a classe passada substitui a do variant em vez de somar.',
            '<code>live</code> é o que acrescenta <code>role="alert"</code>. Um aviso que já estava na página quando ela abriu não deve interromper a leitura; um que aparece depois do envio, sim.',
            '<code>dismissible</code> marca o bloco com <code>data-alert</code>, e o <code>initUi()</code> cuida do resto — sem ele, nenhum listener é registrado.',
        ],
    ],
    [
        'name'        => 'checkbox',
        'title'       => 'Checkbox',
        'description' => 'Caixa de marcação com rótulo, dica e erro, no mesmo desenho dos demais campos.',
        'sources'     => ['checkbox'],
        'examples'    => [
            [
                'title' => 'Aceite e opções',
                'code'  => <<<'BLADE'
                    <x-ui.checkbox name="aceite" label="Li e aceito a política de privacidade" required />
                    <x-ui.checkbox name="novidades" value="sim" label="Quero receber novidades" hint="No máximo um e-mail por mês." checked />
                    <x-ui.checkbox name="termos" id="termos-2024" label="Contrato de 2024" error="É preciso aceitar para continuar." />
                    BLADE,
            ],
        ],
        'notes' => [
            'O id sai de <code>name</code> + <code>value</code>, então várias caixas do mesmo campo convivem sem uma roubar o clique da outra.',
            'A cor vem de <code>accent-primary</code>, no controle nativo: sem SVG substituto, o estado marcado continua sendo o do sistema.',
        ],
    ],
    [
        'name'        => 'radio',
        'title'       => 'Radio',
        'description' => 'Escolha única. Mesmo desenho do checkbox, com o <code>value</code> obrigatório — é ele que diz o que a opção envia.',
        'sources'     => ['radio'],
        'examples'    => [
            [
                'title' => 'Um grupo',
                'code'  => <<<'BLADE'
                    <x-ui.radio name="plano" value="lite" label="Lite" checked />
                    <x-ui.radio name="plano" value="pro" label="Pro" hint="Inclui suporte prioritário." />
                    <x-ui.radio name="plano" id="plano-custom" value="sob-medida" label="Sob medida" error="Escolha um plano." required />
                    BLADE,
            ],
        ],
        'notes' => [
            'Um radio sem <code>value</code> enviaria <code>on</code> em qualquer opção do grupo, então o componente exige o valor em vez de escolher um por você.',
            'Depois de um envio recusado, volta marcada a opção que tinha sido escolhida.',
        ],
    ],
    [
        'name'        => 'dropdown',
        'title'       => 'Dropdown',
        'description' => 'Menu de ações preso a um gatilho. Usa o mesmo script do <code>menu</code>, então não traz JavaScript próprio.',
        'sources'     => ['dropdown'],
        'examples'    => [
            [
                'title'  => 'Ações',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.dropdown label="Ações" icon="heroicon-m-ellipsis-horizontal" variant="default" size="base">
                        <x-ui.link href="/editar" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Editar</x-ui.link>
                        <x-ui.link href="/duplicar" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Duplicar</x-ui.link>
                    </x-ui.dropdown>

                    <x-ui.dropdown label="Alinhado à direita" align="end" width="min-w-64">
                        <x-ui.link href="/relatorio" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Relatório mensal</x-ui.link>
                    </x-ui.dropdown>
                    BLADE,
            ],
            [
                'title' => 'Gatilho próprio',
                'code'  => <<<'BLADE'
                    <x-ui.dropdown id="painel-conta">
                        <x-slot:trigger>
                            <x-ui.button variant="ghost" data-menu-dropdown data-state="closed" aria-controls="painel-conta" aria-expanded="false">
                                Minha conta
                            </x-ui.button>
                        </x-slot:trigger>

                        <x-ui.text size="sm" class="px-3 py-2 text-neutral-600">Sessão iniciada</x-ui.text>
                    </x-ui.dropdown>
                    BLADE,
            ],
        ],
        'notes' => [
            'O painel é irmão do gatilho dentro de um <code>[data-menu]</code>, que é o que o script do menu observa: abrir, fechar no clique fora, no <code>Esc</code> e andar com as setas já vêm de lá.',
            'Cada dropdown gera o próprio id, então vários na mesma página não se confundem.',
            'Com <code>x-slot:trigger</code>, o gatilho é seu — passe <code>id</code> no dropdown e repita esse mesmo valor no <code>aria-controls</code> do gatilho: é por ele que o script encontra o painel. Mantenha também <code>data-menu-dropdown</code>, <code>data-state</code> e <code>aria-expanded</code>, que completam o contrato.',
        ],
    ],
    [
        'name'        => 'input',
        'title'       => 'Input',
        'description' => 'Campo de texto com rótulo, dica e erro. Lê sozinho a mensagem que a validação deixou e devolve o que foi digitado no envio anterior.',
        'sources'     => ['input'],
        'examples'    => [
            [
                'title' => 'Rótulo, dica e obrigatório',
                'code'  => <<<'BLADE'
                    <x-ui.input name="nome" label="Nome" placeholder="Como podemos te chamar?" required />
                    <x-ui.input name="email" type="email" label="E-mail" hint="Usamos só para responder." />
                    <x-ui.input name="telefone" type="tel" label="Telefone" icon="heroicon-m-phone" />
                    BLADE,
            ],
            [
                'title' => 'Erro',
                'code'  => <<<'BLADE'
                    <x-ui.input name="cnpj" id="cnpj-do-cliente" label="CNPJ" error="Informe um CNPJ válido." value="00.000.000/0000-00" control-class="font-mono" />
                    BLADE,
            ],
            [
                'title' => 'Máscara',
                'code'  => <<<'BLADE'
                    <x-ui.input name="telefone" label="Telefone" mask="phone" placeholder="(11) 90000-0000" />
                    <x-ui.input name="documento" label="CPF ou CNPJ" mask="cpf-cnpj" />
                    <x-ui.input name="valor" label="Valor" mask="money" placeholder="0,00" />
                    <x-ui.input name="placa" label="Placa" mask="AAA-0A00" />
                    BLADE,
            ],
            [
                'title'  => 'Tamanhos',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.input name="a" size="sm" placeholder="sm" />
                    <x-ui.input name="b" size="base" placeholder="base" />
                    <x-ui.input name="c" size="lg" placeholder="lg" />
                    BLADE,
            ],
        ],
        'notes' => [
            'Sem <code>error</code>, a mensagem vem do <code>$errors</code> da própria requisição — <code>name="items[0][qty]"</code> é procurado como <code>items.0.qty</code>, que é como o validator guarda. Uma mensagem passada na chamada vence a do validator.',
            'O erro nunca é só a borda vermelha: entra <code>aria-invalid</code>, a mensagem ganha <code>role="alert"</code> e o campo aponta para ela por <code>aria-describedby</code>.',
            'O campo volta preenchido com o envio anterior, exceto quando é <code>type="password"</code> — repopular devolveria a senha digitada para dentro do HTML.',
            '<code>type</code> aceita só os tipos de campo de texto. <code>file</code>, <code>submit</code>, <code>image</code> ou <code>checkbox</code> virariam outro controle dentro de um rótulo que promete texto, então voltam para <code>text</code>.',
            '<code>class</code> veste o bloco inteiro (rótulo, campo e mensagem); <code>control-class</code> veste só o campo.',
            '<code>mask</code> aceita um nome pronto — <code>phone</code>, <code>cpf</code>, <code>cnpj</code>, <code>cpf-cnpj</code>, <code>cep</code>, <code>date</code>, <code>time</code>, <code>money</code>, <code>percent</code>, <code>card</code> — ou um padrão escrito, onde <code>0</code> é dígito e <code>a</code> é letra.',
            'Junto da máscara vai o <code>inputmode</code>: no celular, campo de dígitos abre o teclado numérico em vez do alfabético.',
            'O padrão passado é filtrado antes de virar atributo, então um valor vindo do banco ou da query string não consegue injetar markup ali.',
            '<code>phone</code> e <code>cpf-cnpj</code> aceitam os dois comprimentos: a máscara acompanha o que está sendo digitado.',
            'A biblioteca (<code>imask</code>) é importada só quando existe campo com máscara na página. Instale com <code>npm install imask</code>.',
        ],
    ],
    [
        'name'        => 'textarea',
        'title'       => 'Textarea',
        'description' => 'Campo de texto longo. Mesmo rótulo, dica e erro do input, e cresce com o que é digitado.',
        'sources'     => ['textarea'],
        'examples'    => [
            [
                'title' => 'Mensagem',
                'code'  => <<<'BLADE'
                    <x-ui.textarea name="mensagem" label="Mensagem" rows="5" placeholder="Conte o que você precisa" />
                    <x-ui.textarea name="obs" id="observacoes" label="Observações" hint="Opcional." error="Passou de 500 caracteres." />
                    <x-ui.textarea name="resumo" label="Resumo" size="sm" required control-class="font-mono" />
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>field-sizing-content</code> faz a caixa acompanhar o texto; <code>rows</code> continua valendo como altura inicial.',
            'O conteúdo sai do slot; sem slot, volta o que foi enviado da última vez.',
        ],
    ],
    [
        'name'        => 'select',
        'title'       => 'Select',
        'description' => 'Lista de opções. Aceita <code>options</code> como mapa, como lista ou como linhas vindas do banco.',
        'sources'     => ['select'],
        'examples'    => [
            [
                'title' => 'Opções e placeholder',
                'code'  => <<<'BLADE'
                    <x-ui.select
                        name="assunto"
                        label="Assunto"
                        placeholder="Escolha um assunto"
                        :options="['orcamento' => 'Orçamento', 'suporte' => 'Suporte', 'outro' => 'Outro']"
                        selected="suporte"
                        hint="Responde quem cuida do assunto."
                        required
                    />

                    <x-ui.select
                        name="uf"
                        id="estado"
                        label="Estado"
                        size="sm"
                        control-class="font-mono"
                        error="Escolha um estado."
                        :options="['sp' => 'São Paulo', 'rj' => 'Rio de Janeiro']"
                    />
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>:options="[\'sp\' => \'São Paulo\']"</code>, <code>:options="[\'São Paulo\']"</code> e linhas com <code>value</code>/<code>label</code> (ou <code>id</code>/<code>name</code>) chegam todos na mesma forma, então uma coleção do banco entra sem mapear antes.',
            '<code>placeholder</code> vira uma opção de valor vazio no topo, marcada enquanto nada foi escolhido — com <code>required</code>, é ela que faz o navegador cobrar a escolha.',
            'A seta é um SVG por cima com <code>pointer-events-none</code>: o clique continua abrindo a lista nativa.',
        ],
    ],
    [
        'name'        => 'field',
        'title'       => 'Field',
        'description' => 'O invólucro que o input, o textarea e o select usam por dentro: rótulo, dica e mensagem de erro amarrados ao controle.',
        'sources'     => ['field'],
        'examples'    => [
            [
                'title' => 'Em volta de um controle próprio',
                'code'  => <<<'BLADE'
                    <x-ui.field id="arquivo" label="Currículo" hint="PDF de até 5 MB." required error="Envie o arquivo em PDF.">
                        <input id="arquivo" type="file" name="curriculo" class="text-sm text-neutral-700" />
                    </x-ui.field>
                    BLADE,
            ],
        ],
        'notes' => [
            'Serve para o controle que a lib não cobre — um <code>file</code>, um campo de terceiro — sem perder o rótulo, a dica e o erro no mesmo desenho dos demais.',
            'O <code>id</code> é quem amarra tudo: <code>for</code> no rótulo, <code>-hint</code> e <code>-error</code> no <code>aria-describedby</code> do controle.',
        ],
    ],
    [
        'name'        => 'footer',
        'title'       => 'Footer',
        'description' => 'Rodapé do site: faixa de chamada, colunas de navegação e contato, e a linha legal. Tudo alimentado pelo <code>config/goognet-ui.php</code>.',
        'sources'     => ['footer'],
        'examples'    => [
            [
                'title' => 'Completo',
                'code'  => <<<'BLADE'
                    <x-ui.footer />
                    BLADE,
            ],
            [
                'title' => 'Assinatura própria e uma coluna a mais',
                'code'  => <<<'BLADE'
                    <x-ui.footer :callout="false">
                        <div class="sm:col-span-2 lg:col-span-1">
                            <x-ui.text size="sm" class="text-neutral-500">
                                CNPJ 00.000.000/0001-00 — Av. Paulista, 1000, São Paulo/SP
                            </x-ui.text>
                        </div>

                        <x-slot:credit>
                            <x-ui.text size="sm">
                                Feito por
                                <x-ui.link href="https://goognet.com.br" external underline="hover">Goognet</x-ui.link>
                            </x-ui.text>
                        </x-slot:credit>
                    </x-ui.footer>
                    BLADE,
            ],
            [
                'title' => 'Chamada com outro texto e sem selo',
                'code'  => <<<'BLADE'
                    <x-ui.footer
                        callout-title="Vamos conversar sobre o seu projeto?"
                        callout-text="Atendemos de segunda a sexta."
                        callout-action="Falar no WhatsApp"
                        :validator="false"
                    />
                    BLADE,
            ],
            [
                'title' => 'Sem a faixa de chamada',
                'code'  => <<<'BLADE'
                    <x-ui.footer :callout="false" />
                    BLADE,
            ],
            [
                'title' => 'Com descrição própria',
                'code'  => <<<'BLADE'
                    <x-ui.footer description="Corte a laser e dobra de chapas sob medida." :callout="false" />
                    BLADE,
            ],
        ],
        'notes' => [
            'A faixa de chamada vem antes dos links: quem chegou ao fim está perguntando o que fazer agora. Os textos são props (<code>callout-title</code>, <code>callout-text</code>, <code>callout-action</code>) e <code>:callout="false"</code> tira a faixa — numa política de privacidade, por exemplo. <code>:validator="false"</code> tira o selo do W3C.',
            'Nada é escrito à mão: navegação de <code>goognet-ui.menu</code>, redes de <code>goognet-ui.social</code>, contatos e nome de <code>goognet-ui.company</code>, assinatura de <code>goognet-ui.agency</code>. Coluna sem dado não é renderizada, em vez de sair vazia.',
            'A assinatura sai de <code>goognet-ui.agency</code>, e o slot <code>credit</code> a substitui quando o crédito é um logo, outra frase ou nada disso. Sem nome na config e sem slot, a linha inteira não é renderizada.',
            'O slot padrão vira mais uma coluna na grade — CNPJ, endereço, selo. Conteúdo mais largo se resolve no próprio bloco, com <code>sm:col-span-2</code>.',
            'A marca ocupa uma faixa própria, acima de três colunas de largura igual. Como primeira coluna ela ficava com 473px para 280px de conteúdo — 233px de vão morto ao lado, porque foi dimensionada supondo uma descrição que o boilerplate não traz preenchida.',
            'O link da política aparece em <strong>Institucional</strong>, e é descartado dali se o <code>goognet-ui.menu</code> já o listar: um site que o punha na navegação principal mostrava o mesmo link duas vezes no rodapé.',
            'O botão da faixa é uma <code>&lt;a&gt;</code> vestida de botão, não um <code>&lt;button&gt;</code> dentro de <code>&lt;a&gt;</code> — conteúdo interativo aninhado é HTML inválido, e o validador do W3C acusa.',
            'O link para a política só aparece se a rota <code>privacy</code> existir, então o rodapé não quebra num site que ainda não tem a página.',
            'Os alvos das redes sociais são de 44px no mobile e 40px de <code>sm</code> para cima.',
            'O bloco legal são duas linhas, cada uma abrindo com o seu filete: copyright e <em>voltar ao topo</em> na primeira; selo e crédito da agência na segunda, cada um numa ponta. Numa linha só, agrupadas, elas liam como um bloco solto num canto.',
            'O botão flutuante do WhatsApp é fixo a 12px do canto com 64px, e a última linha é o fim da página. A folga vai <strong>embaixo</strong> (<code>pb-24</code>), não reservada à direita: reservar largura fazia a barra terminar antes das colunas de cima, e era o desalinhamento visível. Medido em 1280px: sem sobreposição, 22px entre a última linha e o botão.',
            '<em>Voltar ao topo</em> usa <code>href="#"</code>, o fragmento vazio: leva ao início do documento. O <code>smooth-anchors.js</code> suaviza e mantém a âncora fora da barra de endereço; sem o script, continua funcionando, só que instantâneo.',
            'O selo do <strong>W3C Validator</strong> aponta para a <strong>página em que está</strong>, não para a raiz do site: <code>url()-&gt;current()</code>, que já descarta a query string — parâmetro de rastreio não faz parte do que se valida e quebraria a busca do validador. O <code>rel</code> leva <code>nofollow</code>, porque selo de saída não deve passar ranking.',
        ],
    ],
    [
        'name'        => 'image',
        'title'       => 'Image',
        'description' => 'Imagem responsiva. O plugin <code>images()</code> do <code>vite.config.js</code> <strong>do site</strong> corta cada jpg/png em 400/800/1200/1600 e em webp; o componente monta o <code>&lt;picture&gt;</code> a partir do que existe no disco.',
        'sources'     => ['image'],
        'examples'    => [
            [
                'title' => 'Imagem de conteúdo',
                'code'  => <<<'BLADE'
                    <x-ui.image src="https://picsum.photos/id/1015/1200/675" alt="Exemplo" class="w-full rounded-lg" />
                    BLADE,
            ],
            [
                'title' => 'Vetor',
                'code'  => <<<'BLADE'
                    <x-ui.image src="https://cdn.simpleicons.org/laravel/FF2D20" alt="Logo" class="h-10" />
                    BLADE,
            ],
            /**
             * Daqui para baixo o exemplo é só código, não prévia: os dois dependem de um jpg
             * em `resources/images`, e o boilerplate não traz foto de exemplo no disco.
             */
            [
                'title'  => 'Imagem principal da página',
                'render' => false,
                'code'   => <<<'BLADE'
                    <x-ui.image
                        src="hero.jpg"
                        alt="Exemplo"
                        sizes="(min-width: 768px) 50vw, 100vw"
                        class="w-full rounded-lg"
                        eager
                    />
                    BLADE,
            ],
            [
                'title'  => 'Restringindo as larguras',
                'render' => false,
                'code'   => <<<'BLADE'
                    <x-ui.image src="hero.jpg" alt="Exemplo" :widths="[400, 800]" class="w-full rounded-lg" />
                    BLADE,
            ],
        ],
        'notes' => [
            'As prévias deste catálogo usam <code>picsum.photos</code>, e os dois últimos exemplos não têm prévia: o boilerplate é um template e não carrega foto de exemplo no disco. Como URL externa não tem cópias para escolher, ela sai como <code>&lt;img&gt;</code> simples — ou seja, <strong>a prévia acima não mostra o <code>&lt;picture&gt;</code></strong>. Aponte o <code>src</code> para um jpg/png seu em <code>resources/images</code> para ver o <code>srcset</code> montado.',
            'Nome sem barra vive em <code>resources/images</code>; com barra, o caminho vai como veio. SVG, URL externa e arquivo sem cópias no disco saem como <code>&lt;img&gt;</code> simples, sem <code>&lt;picture&gt;</code>.',
            'As larguras do <code>srcset</code> vêm do que está no disco, não de uma lista fixa: o plugin não amplia imagem, então fonte de 900px gera só 400 e 800. O prop <code>widths</code> apenas <strong>restringe</strong> esse conjunto.',
            '<code>width</code> e <code>height</code> saem de <code>getimagesize</code> no arquivo de origem — é o que reserva a caixa e evita o salto de layout. Arquivo ausente, sem medida: o componente cai no <code>&lt;img&gt;</code> simples em vez de emitir um <code>&lt;source&gt;</code> quebrado.',
            'Padrão é <code>loading="lazy"</code>. Use <code>eager</code> só na imagem que é candidata a LCP: ela liga <code>fetchpriority="high"</code>, que perde o sentido se estiver em todas.',
            '<code>sizes</code> é <code>100vw</code> por padrão. Se a imagem não ocupa a largura toda, informe — o navegador escolhe o candidato por esse valor, não pelo CSS.',
            'Compressão: webp em <code>quality: 75, effort: 6</code> e o formato original em <code>quality: 80</code> com mozjpeg. São escalas diferentes — webp 80 sai <strong>maior</strong> que mozjpeg 80 na mesma foto, o que faria o navegador preferir o arquivo mais pesado pelo <code>&lt;source&gt;</code>. Medido numa foto de 2400px cortada em 1200: mozjpeg 80 = 168,6 kB, webp 80 = 178,6 kB, webp 75 = 140,0 kB.',
            '<code>background-image</code> no CSS funciona sem build: com <code>npm run dev</code>, o plugin processa a pasta ao subir e gera as cópias de arquivo novo em ~2s. Referência para arquivo que não existe em <code>resources/images</code> <strong>quebra o build</strong> de propósito — o padrão do Vite é só avisar e deixar o caminho quebrado ir para produção.',
            'As cópias com sufixo de largura são geradas e estão no <code>.gitignore</code>. O <code>.webp</code> em tamanho cheio continua versionado, porque um <code>.webp</code> pode ser arquivo de origem.',
            'O <code>x-ui.brand</code> tem a sua própria regra de webp: para jpg/png local ele emite o <code>&lt;source&gt;</code> sem conferir o disco. URL externa não recebe <code>&lt;picture&gt;</code> — não há irmão neste disco para apontar. São contratos diferentes, de propósito.',
        ],
    ],
    [
        'name'        => 'rating',
        'title'       => 'Rating',
        'description' => 'Nota em estrelas. Sem <code>name</code> exibe um número; com <code>name</code> vira campo de formulário, com hover e seleção só em CSS.',
        'sources'     => ['rating'],
        'examples'    => [
            [
                'title'  => 'Exibir uma nota',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.rating :value="4.5" />
                    <x-ui.rating :value="4.3" />
                    <x-ui.rating :value="3" shape="heart" />
                    <x-ui.rating :value="7" :max="10" size="sm" />
                    BLADE,
            ],
            [
                'title'  => 'Campo de formulário',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.rating name="atendimento" label="Como foi o atendimento?" :value="4" />
                    <x-ui.rating name="entrega" clearable />
                    <x-ui.rating name="travado" :value="3" disabled />
                    BLADE,
            ],
            [
                'title'  => 'Tamanhos',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.rating :value="4" size="xs" />
                    <x-ui.rating :value="4" size="sm" />
                    <x-ui.rating :value="4" size="base" />
                    <x-ui.rating :value="4" size="lg" />
                    <x-ui.rating :value="4" size="xl" />
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>max</code> tem teto de 10. Cada ponto vira um SVG renderizado no servidor, e um valor sem limite derrubava a página.',
            'A presença de <code>name</code> é o que decide: sem ele sai um <code>&lt;span role="img"&gt;</code> com <code>aria-label</code>; com ele sai um <code>&lt;fieldset&gt;</code> de radios, navegável pelo teclado como qualquer grupo de radio.',
            'Na exibição, o preenchimento é uma camada recortada por porcentagem, não meia estrela: <code>:value="4.3"</code> desenha 86% e lê como 4,3. Valor fora da escala é grampeado nas pontas.',
            'No campo, as estrelas estão no HTML de <code>max</code> para 1 e são reviradas com <code>flex-row-reverse</code>. É isso que faz o CSS puro funcionar: um input marcado só alcança os irmãos <strong>seguintes</strong>, então as estrelas menores precisam vir depois dele.',
            'Os estados moram em <code>[data-rating]</code> no <code>ui.css</code>, junto do tema do Swiper, e não em utilitárias: a prévia do hover precisa vencer a seleção atual, e utilitária sai na ordem do framework, não na ordem em que foi escrita no elemento.',
            '<code>clearable</code> acrescenta um radio de valor vazio, para limpar a nota enviar o campo em branco em vez de sumir do payload.',
            'Cada grupo gera ids próprios, então dois ratings convivem na mesma página sem um roubar o clique do outro.',
        ],
    ],
    [
        'name'        => 'heading',
        'title'       => 'Heading',
        'description' => 'Título. O <code>level</code> decide a semântica, o <code>size</code> decide o tamanho — os dois são separados de propósito.',
        'sources'     => ['heading'],
        'examples'    => [
            [
                'title' => 'Os quatro tamanhos',
                'code'  => <<<'BLADE'
                    <x-ui.heading size="base">Rótulo de campo</x-ui.heading>
                    <x-ui.heading size="lg">Título de card</x-ui.heading>
                    <x-ui.heading size="xl">Título de seção</x-ui.heading>
                    <x-ui.heading size="2xl">Título da página</x-ui.heading>
                    BLADE,
            ],
            [
                'title' => 'Com nível, entra no sumário da página',
                'code'  => <<<'BLADE'
                    <x-ui.heading :level="2" size="xl">Seção de verdade</x-ui.heading>
                    BLADE,
            ],
            [
                'title' => 'Cor no ponto de uso',
                'code'  => <<<'BLADE'
                    <x-ui.heading size="xl" class="text-primary-ink">Destaque da marca</x-ui.heading>
                    BLADE,
            ],
        ],
        'notes' => [
            'Sem <code>level</code> ele rende <code>&lt;div&gt;</code>, não <code>&lt;h?&gt;</code>. É deliberado: título de card que não é subdivisão do documento não deve entrar no sumário que o leitor de tela percorre. Quando for seção de verdade, passe <code>:level="2"</code>.',
            'Tamanho e nível são separados: uma <code>h2</code> pode ser pequena e um rótulo pode ser grande. Amarrar os dois obrigaria a página a escolher entre o sumário certo e a proporção certa.',
            'A escala é a que as páginas já usavam — <code>2xl</code> é o título da política, <code>xl</code> o de seção, <code>lg</code> o do modal, <code>base</code> um rótulo.',
            'Cor vem por classe: <code>class="text-primary-ink"</code>. O componente não tem prop de cor, pela mesma razão do <code>x-ui.badge</code> — cor semântica se escreve onde tem significado.',
            'Vale a regra de <code>.ai/rules/views.md</code>: uma <code>h1</code> por página, e <code>&lt;section&gt;</code> abre com <code>&lt;header&gt;</code> em volta do título.',
        ],
    ],
    [
        'name'        => 'text',
        'title'       => 'Text',
        'description' => 'Texto de corpo. Rende <code>&lt;p&gt;</code>, ou <code>&lt;span&gt;</code> com <code>inline</code> quando está dentro de uma frase.',
        'sources'     => ['text'],
        'examples'    => [
            [
                'title' => 'Tamanhos',
                'code'  => <<<'BLADE'
                    <x-ui.text size="sm">Pequeno, para apoio.</x-ui.text>
                    <x-ui.text>Padrão, para corpo de texto.</x-ui.text>
                    <x-ui.text size="lg">Maior, para abertura de página.</x-ui.text>
                    <x-ui.text size="xl">Destaque.</x-ui.text>
                    BLADE,
            ],
            [
                'title' => 'Tom',
                'code'  => <<<'BLADE'
                    <x-ui.text variant="strong">Texto forte, para o que precisa pesar.</x-ui.text>
                    <x-ui.text>Texto padrão.</x-ui.text>
                    <x-ui.text variant="subtle">Texto discreto, para apoio.</x-ui.text>
                    BLADE,
            ],
            [
                'title' => 'Dentro de uma frase',
                'code'  => <<<'BLADE'
                    <x-ui.text>
                        O prazo é de
                        <x-ui.text variant="strong" inline>cinco dias úteis</x-ui.text>
                        a partir da confirmação.
                    </x-ui.text>
                    BLADE,
            ],
        ],
        'notes' => [
            'A cor padrão só é aplicada se a classe não trouxer outra. Sem isso, <code>class="text-blue-700"</code> perdia para o cinza do componente: no CSS gerado, <code>blue</code> vem antes de <code>neutral</code>, e vence quem vem depois na folha.',
            'Com <code>inline</code> vira <code>&lt;span&gt;</code>, e perde <code>leading-relaxed</code> e <code>text-pretty</code>: espaçamento de linha e rebalanceamento das últimas linhas são de bloco, não de trecho.',
            'O <code>variant</code> é tom, não cor. O Flux oferece dezessete nomes de paleta aqui; esta lib mantém cor no ponto de uso — <code>class="text-red-700"</code> — para não virar uma lista de cores a manter.',
            'O <code>subtle</code> é <code>neutral-600</code> e não um cinza mais claro: a 16px sobre branco ele mede 7,56:1, enquanto <code>neutral-400</code> mede 2,6 e reprova nos 4,5:1 que corpo de texto deve.',
        ],
    ],
    [
        'name'        => 'link',
        'title'       => 'Link',
        'description' => 'Âncora de texto. A cor de repouso é herdada do contexto; a variante pinta só o hover, para o mesmo link servir em fundo claro e escuro.',
        'sources'     => ['link'],
        'examples'    => [
            [
                'title'  => 'Variantes de hover',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.link href="#">primary</x-ui.link>
                    <x-ui.link href="#" variant="secondary">secondary</x-ui.link>
                    <x-ui.link href="#" variant="neutral">neutral</x-ui.link>
                    <x-ui.link href="#" variant="none">sem cor no hover</x-ui.link>
                    BLADE,
            ],
            [
                'title'  => 'Sublinhado, ícone e ícone puro',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.link href="#" underline="always">sempre sublinhado</x-ui.link>
                    <x-ui.link href="#" underline="none">sem sublinhado</x-ui.link>
                    <x-ui.link href="#" icon="heroicon-m-phone">(11) 3602-6440</x-ui.link>
                    <x-ui.link href="#" icon-trailing="heroicon-m-arrow-top-right-on-square" external>abre em outra aba</x-ui.link>
                    <x-ui.link href="#" icon="ri-instagram-line" label="Instagram" />
                    BLADE,
            ],
            [
                'title'  => 'Tamanhos',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.link href="/x" size="xs">Extra pequeno</x-ui.link>
                    <x-ui.link href="/x" size="sm">Pequeno</x-ui.link>
                    <x-ui.link href="/x" size="base">Base</x-ui.link>
                    <x-ui.link href="/x" size="lg">Grande</x-ui.link>
                    BLADE,
            ],
        ],
        'notes' => [
            'Todo <code>href</code> passa por <code>SafeUrl</code>: aceita <code>http</code>, <code>https</code>, <code>mailto</code>, <code>tel</code>, âncora e caminho relativo. <code>javascript:</code> e afins — inclusive com tab ou quebra de linha no meio — são descartados e o link fica sem destino. O mesmo filtro vale para <code>formaction</code>, <code>xlink:href</code> e demais atributos repassados.',
            'Sem conteúdo no slot o link vira só ícone: o sublinhado some e o <code>label</code> entra como texto de leitor de tela.',
            '<code>external</code> (ou <code>target="_blank"</code>) já acrescenta <code>rel="noopener noreferrer"</code>.',
        ],
    ],
    [
        'name'        => 'brand',
        'title'       => 'Brand',
        'description' => 'Logo do site, com nome opcional ao lado. O <code>logo</code> é o arquivo que você quer — sem convenção de nome — ou markup pelo slot de mesmo nome.',
        'sources'     => ['brand'],
        'examples'    => [
            [
                'title'  => 'Padrão e com link',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.brand class="h-8 w-auto" />
                    <x-ui.brand :href="url('/')" class="h-8 w-auto" />
                    BLADE,
            ],
            [
                'title'  => 'Arquivo ou URL, alt e link externo',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.brand logo="https://cdn.simpleicons.org/laravel/FF2D20" alt="Marca do cliente" class="h-8 w-auto" />
                    <x-ui.brand logo="https://cdn.simpleicons.org/vuedotjs" alt="Outra marca" class="h-8 w-auto" />
                    <x-ui.brand href="https://goognet.com.br" external alt="Site da agência" class="h-8 w-auto" />
                    BLADE,
            ],
            [
                'title' => 'Símbolo com o nome ao lado',
                'code'  => <<<'BLADE'
                    <x-ui.brand logo="https://cdn.simpleicons.org/laravel/FF2D20" name="Acme Inc." class="size-8" />
                    BLADE,
            ],
            [
                'title' => 'Marca própria pelo slot, sem arquivo',
                'code'  => <<<'BLADE'
                    <x-ui.brand href="/" name="Launchpad">
                        <x-slot:logo class="bg-primary size-8 rounded-lg text-sm font-bold text-neutral-950">
                            GN
                        </x-slot:logo>
                    </x-ui.brand>
                    BLADE,
            ],
        ],
        'notes' => [
            'Sem <code>logo</code> ele usa <code>goognet-ui.company.logo</code> — o mesmo nome que alimenta o <code>logo</code> do JSON-LD, num lugar só, para o cabeçalho, o rodapé e o schema não divergirem. O boilerplate deixa essa config <strong>vazia</strong> e não versiona logo nenhum: sem arquivo, o componente rende o nome da empresa como letreiro, em vez de quebrar a página num caminho que não existe.',
            'O <code>logo</code> aponta o arquivo direto: <code>logo="minha-marca.svg"</code>. Nome sem barra procura em <code>resources/images</code>; com barra, vale como está. URL (<code>https://</code>, <code>//</code> ou <code>data:</code>) vai para o <code>src</code> como veio — é o que os exemplos acima usam, para o template não carregar logo de exemplo. Não existe sufixo nem variante a decorar.',
            'O <code>logo</code> é prop <strong>e</strong> slot, como no Flux. Escrito como atributo é caminho de arquivo; como <code>&lt;x-slot:logo&gt;</code> é markup — SVG inline, ícone, letra. Se vierem os dois, o slot vence.',
            'Com <code>name</code> o <code>alt</code> da imagem vira vazio. A palavra já está na tela; um <code>alt</code> repetindo faz o leitor de tela anunciar a empresa duas vezes seguidas. <code>alt</code> explícito continua valendo, para marca que diz algo que o nome não diz.',
            'O slot <code>logo</code> substitui a imagem por completo: SVG inline, ícone ou letra. As classes do slot vão para a caixa dele, então quem chama controla tamanho e cor.',
            'O nome não tem tamanho de fonte próprio — herda o do texto em volta. O mesmo componente lê certo numa barra de 14px e num rodapé de 18px sem prop para isso.',
        ],
    ],
    [
        'name'        => 'cookie-consent',
        'title'       => 'Cookie consent',
        'description' => 'Aviso de cookies. Não bloqueia nada: registra que o visitante foi informado e some. Renderizado no servidor, então quem já aceitou nunca recebe o markup.',
        'sources'     => ['cookie-consent'],
        'examples'    => [
            [
                'title' => 'Padrão',
                'code'  => <<<'BLADE'
                    <x-ui.cookie-consent class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200" />
                    BLADE,
            ],
            [
                'title' => 'Texto e nome do cookie próprios',
                'code'  => <<<'BLADE'
                    <x-ui.cookie-consent name="aviso_lgpd" class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200">
                        Este site usa cookies para medir audiência. Ao continuar, você concorda com a política de privacidade.
                    </x-ui.cookie-consent>
                    BLADE,
            ],
            [
                'title' => 'Apontando para outra política',
                'code'  => <<<'BLADE'
                    <x-ui.cookie-consent
                        policy="/termos"
                        class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200"
                    />
                    BLADE,
            ],
        ],
        'notes' => [
            'A decisão de exibir é feita no PHP, lendo o cookie. Esconder por JavaScript faria o aviso piscar em toda página, antes do script rodar.',
            'O cookie é escrito pelo navegador em texto puro e lido no Blade, então precisa ficar fora da criptografia de cookies do Laravel. O pacote registra essa exceção sozinho, pelo nome em <code>goognet-ui.cookie_consent.name</code>. Por isso o nome se troca <strong>no config</strong>: com a prop <code>name</code> diferente, a exceção não acompanha e o banner não some.',
            'Nada é bloqueado antes do aceite: o clique só grava <code>cookie_consent=accepted</code> por um ano, em <code>Path=/</code> e <code>SameSite=Lax</code>, e remove o card.',
            'Tem duas formas. Em tela estreita é uma barra colada no rodapé, em largura total: um card flutuando cem pixels acima do fundo lê como sobra de layout. De <code>sm</code> para cima vira card no canto inferior esquerdo, longe do botão flutuante do WhatsApp.',
            'O botão do WhatsApp sobe pela altura <strong>real</strong> da barra, não por um valor fixo: o script publica <code>--cookie-consent-height</code> com um <code>ResizeObserver</code>, e o <code>ui.css</code> usa isso dentro de <code>body:has([data-cookie-consent])</code>. O texto quebra em mais linhas em telas menores, então um deslocamento fixo erraria. Medido em 390px: barra de 183px, botão 12px acima dela; ao aceitar, a variável é removida e o botão volta para 12px do fundo.',
            'O empurrão depende de <code>data-floating</code> no elemento fixo. Outro botão flutuante que precise do mesmo tratamento é só marcar igual.',
            'O link "Saber mais" aponta para a rota <code>privacy</code> quando ela existe, e some quando não existe. Passe <code>policy</code> para apontar para outro lugar.',
            'Nos exemplos acima o <code>static!</code> tira o card do <code>fixed</code> só para ele aparecer dentro do catálogo.',
        ],
    ],
    [
        'name'        => 'container',
        'title'       => 'Container',
        'description' => 'Faixa central de conteúdo, com a mesma largura máxima e o mesmo respiro lateral do resto do site.',
        'sources'     => ['container'],
        'examples'    => [
            [
                'title' => 'Uso',
                'code'  => <<<'BLADE'
                    <x-ui.container class="bg-neutral-100 py-4">Conteúdo alinhado ao grid do site</x-ui.container>
                    BLADE,
            ],
        ],
    ],
    [
        'name'        => 'breadcrumb',
        'title'       => 'Breadcrumb',
        'description' => 'Trilha de navegação. Emite o JSON-LD de BreadcrumbList junto, para o Google entender a hierarquia.',
        'sources'     => ['breadcrumb'],
        'examples'    => [
            [
                'title' => 'Trilha simples',
                'code'  => <<<'BLADE'
                    <x-ui.breadcrumb :items="[
                        ['label' => 'Início', 'url' => '/'],
                        ['label' => 'Serviços', 'url' => '/servicos'],
                        ['label' => 'Consultoria'],
                    ]" />
                    BLADE,
            ],
            [
                'title' => 'Separador, ícones e tamanho',
                'code'  => <<<'BLADE'
                    <x-ui.breadcrumb
                        separator="heroicon-m-arrow-right"
                        size="sm"
                        :items="[
                            ['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'],
                            ['label' => 'Blog', 'url' => '/blog', 'icon' => 'heroicon-m-newspaper'],
                            ['label' => 'Artigo'],
                        ]"
                    />
                    BLADE,
            ],
            [
                'title' => 'Variantes de hover',
                'code'  => <<<'BLADE'
                    <x-ui.breadcrumb
                        variant="secondary"
                        :items="[['label' => 'Início', 'url' => '/'], ['label' => 'Serviços']]"
                    />
                    BLADE,
            ],
        ],
        'notes' => [
            'O último item nunca vira link, mesmo carregando <code>url</code>: ele recebe <code>aria-current="page"</code>.',
        ],
    ],
    [
        'name'        => 'pagination',
        'title'       => 'Pagination',
        'description' => 'Navegação entre páginas de um paginador do Laravel. Recebe o próprio <code>$paginator</code> e desenha o resumo, as páginas e as setas.',
        'sources'     => ['pagination'],
        'examples'    => [
            [
                'title' => 'Um paginador completo',
                'code'  => <<<'BLADE'
                    <x-ui.pagination :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 300, 10, 7, ['path' => '/exemplo'])" />
                    BLADE,
            ],
            [
                'title' => 'Tamanhos e raio',
                'code'  => <<<'BLADE'
                    <x-ui.pagination
                        size="sm"
                        :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 90, 10, 4, ['path' => '/exemplo'])"
                    />
                    <x-ui.pagination
                        size="lg"
                        rounded="full"
                        :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 90, 10, 4, ['path' => '/exemplo'])"
                    />
                    BLADE,
            ],
            [
                'title' => 'Só anterior e próxima, sem resumo',
                'code'  => <<<'BLADE'
                    <x-ui.pagination
                        simple
                        :summary="false"
                        :paginator="new Illuminate\Pagination\LengthAwarePaginator(range(1, 10), 300, 10, 7, ['path' => '/exemplo'])"
                    />
                    BLADE,
            ],
        ],
        'notes' => [
            'O prop <code>paginator</code> é o objeto que o controller já devolve — <code>Model::query()->paginate()</code>, <code>simplePaginate()</code> ou <code>cursorPaginate()</code>. Nada de passar página e total soltos.',
            'Sem mais de uma página o componente não desenha nada, como o <code>links()</code> do Laravel. Uma barra de paginação com uma página só é ruído.',
            'As páginas numeradas dependem de o paginador saber o total. <code>simplePaginate()</code> e <code>cursorPaginate()</code> não sabem, então caem sozinhos em anterior/próxima — o <code>simple</code> força esse formato também num paginador completo.',
            'A janela de páginas e as reticências vêm do <code>UrlWindow</code> do próprio Laravel, então a régua é a mesma das views de paginação de fábrica. Quantas páginas aparecem de cada lado se ajusta no paginador, com <code>->onEachSide(2)</code>.',
            'Num celular a barra é só a página atual entre as duas setas; a partir de <code>sm</code> a janela inteira volta. O padrão do Laravel são até 15 células, que empilhavam em três linhas numa tela estreita. Os números escondidos continuam no HTML, como <code>hidden</code>, então um buscador acha todas as páginas do mesmo jeito.',
            'O resumo é a frase <code>Mostrando 61–70 de 300</code>. Desligue com <code>:summary="false"</code>; ele já não aparece quando o paginador não sabe o total.',
            'Cada célula tem a altura e a largura mínima dos tokens de controle (<code>--spacing-control-sm</code>), então o alvo de toque passa o mínimo do WCAG 2.2 e a linha não muda de largura entre a primeira página e as outras.',
            'Para o <code>$posts->links()</code> desenhar este componente sem mexer em nenhuma chamada, registre a view adaptadora no <code>AppServiceProvider</code>: <code>Paginator::defaultView(\'goognet-ui::pagination\')</code>.',
            '<code>Ui::pagination()</code> define padrões e classes por parte: <code>base</code>, <code>summary</code>, <code>list</code>, <code>item</code>, <code>number</code>, <code>page</code>, <code>current</code>, <code>gap</code>, <code>arrow</code> e <code>disabled</code>.',
            'O texto — o resumo, o nome da navegação e os rótulos das setas — vem dos arquivos de idioma, na seção <strong>Idiomas</strong>, no topo.',
        ],
    ],
    [
        'name'        => 'menu',
        'title'       => 'Menu',
        'description' => 'Navegação principal. No desktop abre dropdown ou megamenu; abaixo de lg vira hambúrguer com gaveta e acordeão.',
        'sources'     => ['menu'],
        'examples'    => [
            [
                'title' => 'Links, dropdown e megamenu',
                'code'  => <<<'BLADE'
                    <x-ui.menu :items="[
                        ['label' => 'Início', 'url' => '/'],
                        ['label' => 'Serviços', 'columns' => 2, 'groups' => [
                            ['label' => 'Contábil', 'children' => [
                                ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase', 'description' => 'Planejamento tributário'],
                                ['label' => 'Auditoria', 'url' => '/auditoria', 'icon' => 'heroicon-m-document-check', 'description' => 'Revisão de demonstrativos'],
                            ]],
                        ]],
                        ['label' => 'Blog', 'children' => [
                            ['label' => 'Artigos', 'url' => '/artigos'],
                            ['label' => 'Novidades', 'url' => '/novidades'],
                        ]],
                        ['label' => 'Contato', 'url' => '/contato'],
                    ]">
                        <x-ui.button variant="primary" href="/orcamento" class="w-full">Peça um orçamento</x-ui.button>
                    </x-ui.menu>
                    BLADE,
            ],
            [
                'title' => 'Etiqueta no item e estado decidido à mão',
                'code'  => <<<'BLADE'
                    <x-ui.menu :items="[
                        ['label' => 'Início', 'url' => '/'],
                        ['label' => 'Blog', 'url' => '/blog', 'badge' => 'Novo'],
                        ['label' => 'Vagas', 'url' => '/vagas', 'badge' => ['label' => '2', 'variant' => 'primary']],
                        ['label' => 'Contato', 'url' => '/contato', 'current' => true],
                    ]" />
                    BLADE,
            ],
        ],
        'notes' => [
            'Cada item aceita <code>url</code> (caminho literal) ou <code>route</code> (nome da rota). Prefira <code>route</code>: caminho literal em <code>config/goognet-ui.php</code> precisa ser lembrado em dois lugares, e se você mudar a rota o menu continua apontando para o endereço velho sem avisar.',
            'O <code>route</code> não pode ser resolvido dentro do <code>config/goognet-ui.php</code> — config é lido no bootstrap, antes de o roteador existir, e <code>route()</code> ali morre com <code>Argument #2 ($request) must be of type Request, null given</code>. Com <code>config:cache</code> seria pior: a URL ficaria congelada com o domínio da máquina que rodou o comando. Por isso o componente guarda o nome e resolve na renderização.',
            'Com parâmetro: <code>[\'route\' => [\'posts.show\', [\'slug\' => \'meu-post\']]]</code>. Rota inexistente estoura dizendo qual nome e qual item — funciona dentro de dropdown e megamenu também.',
            'O item atual acende também nas páginas abaixo dele: em <code>/blog/meu-artigo</code> o item <code>Blog</code> continua marcado. Casamento exato sozinho deixava toda página de artigo com a barra inteira apagada.',
            'A barra final no prefixo é o que impede <code>/blog</code> de roubar <code>/blog-antigo</code>. E a home nunca entra como prefixo: todo endereço do site começa nela, então ela acenderia em tudo.',
            'Passe <code>current</code> no item para decidir à mão — <code>true</code> para uma landing que pertence a uma seção sem estar abaixo dela, <code>false</code> para apagar uma seção numa página dela mesma.',
            'O <code>badge</code> aceita string ou array com cor: <code>[\'label\' => \'2\', \'variant\' => \'primary\']</code>. Vai para o <code>x-ui.badge</code>, então o vocabulário é o mesmo do resto da lib, e funciona tanto em link quanto em gatilho de dropdown.',
            'A gaveta do celular marca a página atual como linha preenchida, não como traço. Antes ela não marcava nada, e no telefone o menu nunca dizia onde a pessoa estava.',
            'Um item com <code>children</code> vira dropdown; com <code>groups</code> vira megamenu. Sem os dois, é link simples.',
            'O slot padrão aparece só no rodapé da gaveta mobile — é onde mora o call-to-action.',
        ],
    ],
    [
        'name'        => 'megamenu',
        'title'       => 'Megamenu',
        'description' => 'Painel largo de navegação, com grupos em colunas. Vive dentro do <code>x-ui.menu</code> quando um item traz <code>groups</code>, e existe solto para quem monta o header à mão.',
        'sources'     => ['megamenu', 'megamenu-panel'],
        'examples'    => [
            [
                'title' => 'O painel, aberto',
                'note'  => 'Dentro do menu ele só aparece ao clicar no gatilho; aqui está o painel isolado, para se ver a anatomia.',
                'code'  => <<<'BLADE'
                    <x-ui.megamenu-panel
                        :columns="2"
                        :groups="[
                            ['label' => 'Contábil', 'children' => [
                                ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase', 'description' => 'Planejamento tributário'],
                                ['label' => 'Auditoria', 'url' => '/auditoria', 'icon' => 'heroicon-m-document-check', 'description' => 'Revisão de demonstrativos'],
                            ]],
                            ['label' => 'Fiscal', 'children' => [
                                ['label' => 'Apuração', 'url' => '/apuracao', 'icon' => 'heroicon-m-calculator', 'description' => 'Mensal e trimestral'],
                                ['label' => 'Obrigações', 'url' => '/obrigacoes', 'icon' => 'heroicon-m-clipboard-document-list'],
                            ]],
                        ]"
                    />
                    BLADE,
            ],
            [
                'title' => 'Uma coluna só',
                'code'  => <<<'BLADE'
                    <x-ui.megamenu-panel
                        :columns="1"
                        :groups="[
                            ['label' => 'Serviços', 'children' => [
                                ['label' => 'Consultoria', 'url' => '/consultoria', 'description' => 'Planejamento tributário'],
                                ['label' => 'Auditoria', 'url' => '/auditoria'],
                            ]],
                        ]"
                    />
                    BLADE,
            ],
            [
                'title' => 'Com gatilho, como no header',
                'code'  => <<<'BLADE'
                    <div class="relative">
                        <x-ui.megamenu
                            label="Serviços"
                            :columns="2"
                            :groups="[
                                ['label' => 'Contábil', 'children' => [
                                    ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase'],
                                ]],
                                ['label' => 'Fiscal', 'children' => [
                                    ['label' => 'Apuração', 'url' => '/apuracao', 'icon' => 'heroicon-m-calculator'],
                                ]],
                            ]"
                        >
                            <x-ui.button variant="primary" href="/orcamento" size="sm">Peça um orçamento</x-ui.button>
                        </x-ui.megamenu>
                    </div>
                    BLADE,
            ],
        ],
        'notes' => [
            'O painel se estende sobre o ancestral posicionado mais próximo, então quem o usa solto precisa de um <code>relative</code> em volta — no <code>x-ui.navbar</code> isso já vem pronto.',
            '<code>columns</code> aceita 1 a 4 e as classes estão escritas por extenso no componente: nome de classe interpolado nunca entra na folha de estilo do Tailwind.',
            'Cada filho aceita <code>icon</code> e <code>description</code>. Sem descrição o item vira uma linha simples, e a lista continua legível.',
            'Abaixo de <code>lg</code> o painel não flutua: cai no fluxo, que é o que o <code>x-ui.menu</code> usa para achatá-lo dentro da gaveta.',
            'O slot fecha o painel com uma chamada — no header costuma ser o botão de orçamento.',
        ],
    ],
    [
        'name'        => 'navbar',
        'title'       => 'Navbar',
        'description' => 'Cabeçalho do site: barra fixa opcional, faixa de contato acima e esconder-ao-rolar.',
        'sources'     => ['navbar'],
        'examples'    => [
            [
                'title' => 'Com faixa de informação e auto-hide',
                'code'  => <<<'BLADE'
                    <x-ui.navbar class="bg-white" auto-hide>
                        <x-slot:info class="bg-primary text-neutral-950">
                            <div class="flex items-center gap-6">
                                <x-ui.link href="tel:1136026440" icon="heroicon-m-phone" variant="neutral">(11) 3602-6440</x-ui.link>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-ui.link href="#" icon="ri-instagram-line" label="Instagram" variant="neutral" />
                            </div>
                        </x-slot>

                        <x-ui.brand :href="url('/')" class="h-8 w-auto" />

                        <x-ui.menu :items="[
                            ['label' => 'Início', 'url' => '/'],
                            ['label' => 'Contato', 'url' => '/contato'],
                        ]" />
                    </x-ui.navbar>
                    BLADE,
            ],
            [
                'title' => 'Estática e sem régua',
                'code'  => <<<'BLADE'
                    <x-ui.navbar position="static" :border="false" class="bg-neutral-50">
                        <x-ui.brand class="h-7 w-auto" />

                        <x-ui.menu :items="[['label' => 'Início', 'url' => '/']]" />
                    </x-ui.navbar>
                    BLADE,
            ],
        ],
        'notes' => [
            'A cor vem por classe Tailwind no ponto de uso (<code>class="bg-neutral-900"</code>), nunca por token. O <code>bg-white</code> padrão sai quando você passa a sua.',
            'A faixa <code>info</code> é <code>hidden md:block</code> — não existe no mobile, igual à referência que o componente copia.',
            'O esconder usa <code>-top-full</code>, então funciona com ou sem faixa. O script ressincroniza quando a barra de URL do mobile redimensiona a viewport.',
        ],
    ],
    [
        'name'        => 'tabs',
        'title'       => 'Tabs',
        'description' => 'Abas em CSS puro, sem JavaScript: radios escondidos guardam o estado e o painel aparece pelo seletor de irmão adjacente.',
        'sources'     => ['tabs', 'tab'],
        'examples'    => [
            [
                'title' => 'Três abas, a segunda aberta',
                'code'  => <<<'BLADE'
                    <x-ui.tabs name="docs-produto">
                        <x-ui.tab label="Descrição">
                            <p>Conteúdo rico, HTML à vontade.</p>
                        </x-ui.tab>

                        <x-ui.tab label="Ficha técnica" icon="heroicon-m-list-bullet" checked>
                            <ul class="list-disc space-y-1 ps-5">
                                <li>Potência: 1.500 W</li>
                                <li>Tensão: 220 V</li>
                                <li>Peso: 12 kg</li>
                            </ul>
                        </x-ui.tab>

                        <x-ui.tab label="Downloads">
                            <ul class="space-y-1">
                                <li><x-ui.link href="#">Manual de instalação (PDF)</x-ui.link></li>
                                <li><x-ui.link href="#">Ficha de segurança (PDF)</x-ui.link></li>
                            </ul>
                        </x-ui.tab>
                    </x-ui.tabs>
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>name</code> é obrigatório: é ele que agrupa os radios. Sem ele o componente lança exceção em vez de renderizar abas que não conversam.',
            'Sem nenhuma aba <code>checked</code>, a primeira lidera — resolvido em CSS, com <code>:not(:has(:checked))</code>.',
            'As setas do teclado navegam entre as abas de graça, por serem radios. É por isso que não são botões.',
            'Atributos extras vão para o painel: <code>&lt;x-ui.tab class="pt-10"&gt;</code>.',
        ],
    ],
    [
        'name'        => 'accordion',
        'title'       => 'Accordion',
        'description' => 'Sanfona sobre <code>&lt;details&gt;</code>/<code>&lt;summary&gt;</code>: o navegador já dá semântica de disclosure, Esc, Enter e busca na página. O atalho <code>faq</code> emite o JSON-LD de FAQPage.',
        'sources'     => ['accordion', 'accordion-item'],
        'examples'    => [
            [
                'title' => 'FAQ com JSON-LD',
                'code'  => <<<'BLADE'
                    <x-ui.accordion name="docs-faq-schema" label="Perguntas frequentes" :faq="[
                        'Qual o prazo de entrega?' => 'De 3 a 5 dias úteis para todo o Brasil.',
                        'Posso parcelar?'          => 'Em até 12x sem juros no cartão.',
                        'Tem garantia?'            => '12 meses de garantia de fábrica.',
                    ]" />
                    BLADE,
            ],
            [
                'title' => 'Exclusivo, com um item aberto',
                'code'  => <<<'BLADE'
                    <x-ui.accordion name="docs-faq" label="Perguntas frequentes">
                        <x-ui.accordion-item label="Qual o prazo de entrega?" open>
                            <p>De 3 a 5 dias úteis para todo o Brasil.</p>
                        </x-ui.accordion-item>

                        <x-ui.accordion-item label="Posso parcelar?" icon="heroicon-m-credit-card">
                            <p>Em até 12x sem juros no cartão.</p>
                        </x-ui.accordion-item>

                        <x-ui.accordion-item label="Tem garantia?">
                            <p>12 meses de garantia de fábrica.</p>
                        </x-ui.accordion-item>
                    </x-ui.accordion>
                    BLADE,
            ],
        ],
        'notes' => [
            'Com <code>name</code> no grupo, abrir um item fecha o outro — é o <code>name</code> nativo do <code>&lt;details&gt;</code>. Sem ele, cada item é independente.',
            'A altura anima por <code>::details-content</code> com <code>interpolate-size</code>. Navegador sem suporte abre seco, sem quebrar nada.',
            'O atalho <code>faq</code> emite o JSON-LD de <code>FAQPage</code> junto. A resposta é texto puro e sai escapada, porque o Google recusa markup dentro de <code>acceptedAnswer</code>. Resposta com HTML vai pelo slot, que não emite schema.',
        ],
    ],
    [
        'name'        => 'modal',
        'title'       => 'Modal',
        'description' => 'Diálogo sobre <code>&lt;dialog&gt;</code> nativo: foco preso, Esc, fundo inerte e top layer vêm do navegador. O script só roteia os cliques.',
        'sources'     => ['modal'],
        'examples'    => [
            [
                'title'  => 'Gatilho, corpo e rodapé',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button variant="primary" data-modal-open="docs-orcamento">Pedir orçamento</x-ui.button>

                    <x-ui.modal name="docs-orcamento" title="Peça um orçamento">
                        <p>Conte o que você precisa e respondemos em até 1 dia útil.</p>

                        <x-slot:footer>
                            <x-ui.button variant="ghost" data-modal-close>Cancelar</x-ui.button>
                            <x-ui.button variant="primary">Enviar</x-ui.button>
                        </x-slot>
                    </x-ui.modal>
                    BLADE,
            ],
            [
                'title'  => 'Travado: só fecha pelo botão',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.button data-modal-open="docs-aviso">Abrir aviso travado</x-ui.button>

                    <x-ui.modal name="docs-aviso" title="Confirme antes de sair" :closable="false" size="sm">
                        <p>Esse não fecha no Esc nem no clique de fora.</p>

                        <x-slot:footer>
                            <x-ui.button variant="primary" data-modal-close>Entendi</x-ui.button>
                        </x-slot>
                    </x-ui.modal>
                    BLADE,
            ],
        ],
        'notes' => [
            'Abre com <code>data-modal-open="nome"</code> em qualquer elemento da página; fecha com <code>data-modal-close</code> dentro do modal.',
            'O scroll da página trava enquanto houver modal aberto e volta quando o último fecha.',
            'Sem <code>title</code> e com <code>:closable="false"</code>, o cabeçalho inteiro deixa de existir.',
        ],
    ],
    [
        'name'        => 'carousel',
        'title'       => 'Carousel',
        'description' => 'Carrossel sobre o Swiper, com lightbox opcional via fslightbox. A configuração vai inteira num <code>data-carousel</code> e o script a lê por instância, então várias galerias convivem na mesma página.',
        'sources'     => ['carousel', 'carousel-slide'],
        'examples'    => [
            [
                'title' => 'Galeria responsiva com autoplay',
                'code'  => <<<'BLADE'
                    <x-ui.carousel
                        label="Galeria"
                        :per-view="['base' => 2, 'md' => 3, 'lg' => 4]"
                        :gap="['base' => 12, 'sm' => 20, 'md' => 24, 'lg' => 32]"
                        autoplay="1800"
                        pagination
                    >
                        @foreach (range(1, 8) as $item)
                            <x-ui.carousel-slide>
                                <div class="flex h-40 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600">
                                    Item {{ $item }}
                                </div>
                            </x-ui.carousel-slide>
                        @endforeach
                    </x-ui.carousel>
                    BLADE,
            ],
            [
                'title' => 'Um por vez, com setas',
                'code'  => <<<'BLADE'
                    <x-ui.carousel label="Depoimentos" :gap="24" navigation pagination class="px-14">
                        <x-ui.carousel-slide>
                            <figure class="rounded-xl border border-neutral-200 bg-white p-6 shadow-soft">
                                <x-ui.rating :value="5" size="sm" />

                                <blockquote class="mt-3 text-neutral-700">
                                    Atendimento rápido e entrega no prazo.
                                </blockquote>

                                <figcaption class="mt-3 text-sm text-neutral-500">Ana Prado</figcaption>
                            </figure>
                        </x-ui.carousel-slide>

                        <x-ui.carousel-slide>
                            <figure class="rounded-xl border border-neutral-200 bg-white p-6 shadow-soft">
                                <x-ui.rating :value="4" size="sm" />

                                <blockquote class="mt-3 text-neutral-700">
                                    Equipamento novo e bem conservado.
                                </blockquote>

                                <figcaption class="mt-3 text-sm text-neutral-500">Caio Menezes</figcaption>
                            </figure>
                        </x-ui.carousel-slide>
                    </x-ui.carousel>
                    BLADE,
            ],
            [
                'title' => 'Altura acompanhando o slide',
                'code'  => <<<'BLADE'
                    <x-ui.carousel label="Depoimentos" :gap="24" auto-height navigation pagination class="px-14">
                        <x-ui.carousel-slide>
                            <figure class="shadow-soft rounded-xl border border-neutral-200 bg-white p-6">
                                <blockquote class="text-neutral-700">Resolveram em um dia.</blockquote>

                                <figcaption class="mt-3 text-sm text-neutral-500">Ana Prado</figcaption>
                            </figure>
                        </x-ui.carousel-slide>

                        <x-ui.carousel-slide>
                            <figure class="shadow-soft rounded-xl border border-neutral-200 bg-white p-6">
                                <blockquote class="text-neutral-700">
                                    Chegamos com o prazo em cima e mesmo assim refizeram o orçamento no mesmo dia.
                                    A equipe montou tudo em duas visitas, deixou o local limpo e ainda voltou na
                                    semana seguinte para conferir o acabamento. É raro encontrar esse cuidado
                                    depois que a nota já foi emitida.
                                </blockquote>

                                <figcaption class="mt-3 text-sm text-neutral-500">Caio Menezes</figcaption>
                            </figure>
                        </x-ui.carousel-slide>
                    </x-ui.carousel>
                    BLADE,
            ],
            [
                'title' => 'Bullets dinâmicos para muitos slides',
                'code'  => <<<'BLADE'
                    <x-ui.carousel label="Fotos" :per-view="['base' => 2, 'md' => 3]" :gap="16" dynamic-bullets>
                        @foreach (range(1, 12) as $item)
                            <x-ui.carousel-slide>
                                <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600">
                                    {{ $item }}
                                </div>
                            </x-ui.carousel-slide>
                        @endforeach
                    </x-ui.carousel>
                    BLADE,
            ],
            [
                'title' => 'Galeria com lightbox',
                'code'  => <<<'BLADE'
                    <x-ui.carousel label="Obras" :per-view="['base' => 2, 'md' => 3]" :gap="16" lightbox="obras">
                        @foreach (range(1, 6) as $item)
                            <x-ui.carousel-slide :source="'https://picsum.photos/id/' . ($item + 10) . '/1200/800'">
                                <img
                                    src="https://picsum.photos/id/{{ $item + 10 }}/400/300"
                                    alt="Obra {{ $item }}"
                                    class="w-full rounded-lg"
                                    loading="lazy"
                                />
                            </x-ui.carousel-slide>
                        @endforeach
                    </x-ui.carousel>
                    BLADE,
            ],
            [
                'title' => 'Loop forçado e fonte de vídeo no lightbox',
                'code'  => <<<'BLADE'
                    <x-ui.carousel label="Vídeos" :per-view="2" :gap="16" :loop="true" lightbox="videos">
                        <x-ui.carousel-slide source="https://youtu.be/exemplo" type="youtube">
                            <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
                                Vídeo 1
                            </div>
                        </x-ui.carousel-slide>

                        <x-ui.carousel-slide source="https://youtu.be/exemplo-2" type="youtube">
                            <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
                                Vídeo 2
                            </div>
                        </x-ui.carousel-slide>

                        <x-ui.carousel-slide source="https://youtu.be/exemplo-3" type="youtube">
                            <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
                                Vídeo 3
                            </div>
                        </x-ui.carousel-slide>

                        <x-ui.carousel-slide source="https://youtu.be/exemplo-4" type="youtube">
                            <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
                                Vídeo 4
                            </div>
                        </x-ui.carousel-slide>
                    </x-ui.carousel>
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>perView</code> e <code>gap</code> aceitam valor único ou mapa por breakpoint do Tailwind (<code>base</code>, <code>sm</code>, <code>md</code>, <code>lg</code>, <code>xl</code>, <code>2xl</code>).',
            'O loop só liga quando há ao menos <code>per-view + 1</code> slides no maior breakpoint — a mesma conta que o Swiper faz. Abaixo disso ele não funciona e o Swiper avisa no console, então o pacote desliga em silêncio, <strong>inclusive com <code>:loop="true"</code></strong>. <code>:loop="false"</code> desliga sempre. Testado contra o Swiper em 48 combinações de slides e <code>per-view</code>: nenhum aviso e nenhum loop desligado sem necessidade.',
            'O Swiper 12 não tem mais a opção <code>lazy</code>. Imagem preguiçosa é <code>loading="lazy"</code> no próprio <code>&lt;img&gt;</code>.',
            'Autoplay pausa no hover pelo <code>pauseOnMouseEnter</code> do Swiper, e não liga quando o sistema pede <code>prefers-reduced-motion: reduce</code>.',
            '<code>auto-height</code> faz a caixa acompanhar a altura do slide em exibição, em vez de todos dividirem a altura do mais alto. Vale para conteúdo de tamanho desigual — depoimento de duas linhas ao lado de um de dez. Numa grade de cartões deixe desligado: ali a altura uniforme é o que alinha a fileira.',
            'Com <code>auto-height</code> e <code>per-view</code> maior que 1, a altura é a do slide mais alto entre os visíveis, não a do ativo.',
            'A altura é animada pelo CSS do próprio Swiper. Sob <code>prefers-reduced-motion: reduce</code> o <code>ui.css</code> tira essa transição: a caixa muda de tamanho, mas sem percorrer o caminho.',
            'A paginação fica fora do <code>.swiper</code> de propósito: o Swiper só posiciona bullets que são filhos diretos do container, e manter fora dispensa <code>!important</code>.',
            'Os bullets têm área de clique de 24px (mínimo da WCAG 2.2), com o ponto visível de 12px desenhado dentro.',
            '<code>dynamic-bullets</code> mostra cinco pontos por vez, encolhendo os das pontas, em vez de uma fileira que cresce sem fim. Vale a partir de umas oito imagens; com poucas, só tira a noção de quantas são.',
            'O <code>dynamicBullets</code> do Swiper escala o próprio bullet — que aqui é o alvo de clique — e os 0,33 dele deixariam um alvo de 8px. O <code>ui.css</code> cancela esse transform e aplica a escala ao ponto: a faixa fica como o Swiper desenha e o alvo continua de 24px.',
            'Pedir <code>dynamic-bullets</code> já liga a paginação. Escrever os dois não é erro, mas escrever só <code>dynamic-bullets</code> também funciona — não existe o caso de pedir e não aparecer nada.',
            'O <code>lightbox</code> usa o pacote <code>fslightbox</code> e só entra na página que tem carrossel com ele: o import é dinâmico, num chunk à parte.',
            'Cada slide precisa do prop <code>source</code> com a imagem grande — o thumb fica no slot. Slide sem <code>source</code> continua slide comum, sem clique.',
            'O valor de <code>lightbox</code> é o nome da galeria e chega ao slide por <code>@aware</code>. Dois carrosséis com o mesmo nome viram uma galeria só; <code>lightbox</code> sem valor usa o nome <code>carousel</code> para todos.',
            'O fsLightbox é carregado <strong>antes</strong> do Swiper de propósito: ele varre o DOM na hora que entra e guarda a ordem que encontrou, e o <code>loop</code> do Swiper move os slides de lugar depois disso.',
            'Vídeo ou fonte que a extensão não denuncia: passe <code>type</code> no slide (<code>image</code>, <code>video</code>, <code>youtube</code>).',
        ],
    ],
    [
        'name'        => 'gallery',
        'title'       => 'Gallery',
        'description' => 'Grade de imagens com lightbox opcional. Mesma gramática do carousel — <code>lightbox</code> no pai, <code>source</code> no item — mas sem trilho: tudo aparece de uma vez.',
        'sources'     => ['gallery', 'gallery-item'],
        'examples'    => [

            [
                'title' => 'Mansonry, cada imagem na sua altura',
                'code'  => <<<'BLADE'
                    <x-ui.gallery masonry lightbox :columns="['base' => 2, 'md' => 3]" :gap="4" label="Obras entregues">
                        @foreach ([500, 800, 620, 900, 540, 720] as $height)
                            <x-ui.gallery-item :src="'https://picsum.photos/seed/m' . $height . '/600/' . $height" alt="" />
                        @endforeach
                    </x-ui.gallery>
                    BLADE,
            ],
            [
                'title' => 'Grade simples',
                'code'  => <<<'BLADE'
                    <x-ui.gallery :columns="['base' => 2, 'md' => 3]" label="Obras">
                        @foreach (range(1, 6) as $item)
                            <x-ui.gallery-item
                                :src="'https://picsum.photos/id/' . ($item + 20) . '/600/450'"
                                :alt="'Obra ' . $item"
                            />
                        @endforeach
                    </x-ui.gallery>
                    BLADE,
            ],
            [
                'title' => 'Com lightbox',
                'code'  => <<<'BLADE'
                    <x-ui.gallery :columns="['base' => 2, 'md' => 4]" :gap="6" lightbox="galeria-obras" label="Obras">
                        @foreach (range(1, 8) as $item)
                            <x-ui.gallery-item
                                :src="'https://picsum.photos/id/' . ($item + 30) . '/600/450'"
                                :source="'https://picsum.photos/id/' . ($item + 30) . '/1600/1200'"
                                :alt="'Obra ' . $item"
                            />
                        @endforeach
                    </x-ui.gallery>
                    BLADE,
            ],
            [
                'title' => 'Primeira imagem urgente, vídeo na grade',
                'code'  => <<<'BLADE'
                    <x-ui.gallery :columns="['base' => 2, 'md' => 3]" lightbox="midia">
                        <x-ui.gallery-item
                            src="https://picsum.photos/id/40/600/450"
                            source="https://picsum.photos/id/40/1600/1200"
                            alt="Fachada"
                            sizes="(min-width: 768px) 33vw, 50vw"
                            eager
                        />

                        <x-ui.gallery-item
                            src="https://picsum.photos/id/41/600/450"
                            source="https://youtu.be/dQw4w9WgXcQ"
                            type="youtube"
                            alt="Tour em vídeo"
                        />

                        <x-ui.gallery-item src="https://picsum.photos/id/42/600/450" alt="Interior" />
                    </x-ui.gallery>
                    BLADE,
            ],
            [
                'title' => 'Conteúdo próprio pelo slot',
                'code'  => <<<'BLADE'
                    <x-ui.gallery :columns="['base' => 1, 'sm' => 3]" :gap="4">
                        @foreach (['Antes', 'Durante', 'Depois'] as $fase)
                            <x-ui.gallery-item>
                                <figure class="shadow-soft rounded-lg border border-neutral-200 bg-white p-4">
                                    <div class="flex h-24 items-center justify-center rounded bg-neutral-100 text-neutral-600">
                                        {{ $fase }}
                                    </div>

                                    <figcaption class="mt-2 text-sm text-neutral-500">{{ $fase }} da obra</figcaption>
                                </figure>
                            </x-ui.gallery-item>
                        @endforeach
                    </x-ui.gallery>
                    BLADE,
            ],
        ],
        'notes' => [
            '<code>type</code> só aceita <code>image</code>, <code>video</code> ou <code>youtube</code>. Qualquer outro valor é descartado.',
            '<code>masonry</code> troca a grade por colunas CSS: cada imagem fica com a altura que tem, em vez de ser recortada na altura da linha. O preço é a ordem de leitura — coluna desce antes de virar, então o segundo item fica embaixo do primeiro, não ao lado.',
            'Na mansonry o espaço entre imagens empilhadas é a margem do próprio item (o <code>gap</code> de coluna não separa linhas), e <code>break-inside-avoid</code> impede que uma imagem seja cortada no pé da coluna.',
            'A galeria é <code>&lt;ul&gt;</code> e o item é <code>&lt;li&gt;</code>: leitor de tela anuncia quantas imagens são. O <code>label</code> vira <code>aria-label</code> e é opcional.',
            '<code>columns</code> aceita número ou mapa por breakpoint do Tailwind, de 1 a 6. <code>gap</code> aceita 2, 3, 4, 5, 6, 8, 10 ou 12 — os valores estão escritos por extenso no componente porque o scanner do Tailwind não enxerga classe montada por interpolação.',
            'Sem <code>source</code>, o próprio <code>src</code> abre no lightbox. Informe <code>source</code> quando existir uma versão maior — é o caso normal: o thumb não precisa ter 1600px.',
            'Item sem <code>src</code> e sem <code>source</code> continua item comum, sem clique. É assim que o slot livre convive com a grade clicável.',
            'O <code>source</code> não precisa ser imagem: com <code>type="youtube"</code> o item vira capa de vídeo dentro da mesma galeria.',
            '<code>eager</code> e <code>sizes</code> vão direto para o <code>x-ui.image</code>. Use <code>eager</code> só na imagem que aparece sem rolar a página.',
            'O nome em <code>lightbox</code> agrupa a galeria. Duas galerias com o mesmo nome viram uma só; <code>lightbox</code> sem valor usa o nome <code>gallery</code>.',
            'O <code>fslightbox</code> é carregado no <code>app.js</code>, antes dos carrosséis: ele varre o DOM ao entrar e guarda a ordem que encontrou. Só entra na página que tem alguma âncora <code>data-fslightbox</code>.',
        ],
    ],
    [
        'name'        => 'map',
        'title'       => 'Map',
        'description' => 'Mapa incorporado num <code>&lt;iframe&gt;</code>. O endereço sai de <code>goognet-ui.location.map</code> por padrão, então a página não repete a URL do embed.',
        'sources'     => ['map'],
        'examples'    => [
            [
                'title' => 'Mapa da configuração',
                'code'  => <<<'BLADE'
                    <x-ui.map src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik" />
                    BLADE,
            ],
            [
                'title' => 'Proporção e título próprios',
                'code'  => <<<'BLADE'
                    <x-ui.map
                        src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
                        title="Onde fica a loja da Avenida Paulista"
                        ratio="wide"
                        class="rounded-xl"
                    />
                    BLADE,
            ],
            [
                'title' => 'Mapa acima da dobra',
                'code'  => <<<'BLADE'
                    <x-ui.map
                        src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
                        eager
                        ratio="square"
                    />
                    BLADE,
            ],
            [
                'title' => 'Altura vinda do pai',
                'code'  => <<<'BLADE'
                    <div class="h-64">
                        <x-ui.map
                            src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
                            :ratio="false"
                            class="size-full"
                        />
                    </div>
                    BLADE,
            ],
        ],
        'notes' => [
            'O iframe leva <code>sandbox</code> sem <code>allow-top-navigation</code>: um clique dentro do mapa não consegue levar a página inteira para outro endereço. Medido com Google e OSM — renderizam igual e o "Abrir no Maps" continua abrindo nova aba.',
            'Só abre hosts de <code>goognet-ui.security.frame_hosts</code> (Google Maps e OpenStreetMap por padrão) e só em <code>https</code>. Um endereço vindo de painel não vira página de outro site dentro do seu.',
            'Sem <code>src</code> ele usa <code>goognet-ui.location.map</code>, que vem de <code>LOCATION_MAP_LINK</code> no <code>.env</code>.',
            'A URL do Google Maps só sai do diálogo <strong>Compartilhar → Incorporar um mapa</strong>: é uma string opaca que começa com <code>/maps/embed?pb=</code> e não dá para escrever à mão. O formato antigo <code>maps.google.com/?output=embed</code> hoje redireciona para uma página com <code>X-Frame-Options: sameorigin</code>, que o navegador recusa enquadrar — por isso os exemplos aqui usam OpenStreetMap, que enquadra sem chave.',
            'Link vazio não renderiza nada. <code>&lt;iframe src=""&gt;</code> não é quadro vazio: o navegador resolve a string vazia contra o documento atual e carrega a própria página dentro da caixa.',
            'O <code>ratio</code> reserva a altura antes dos tiles chegarem. O embed do Google não tem tamanho intrínseco, então sem proporção a caixa fica com altura zero e empurra a página quando termina de carregar — o layout shift que o Core Web Vitals mede.',
            'Valores de <code>ratio</code>: <code>video</code> (padrão), <code>square</code>, <code>wide</code>, <code>tall</code>, qualquer utility <code>aspect-*</code>, ou <code>:ratio="false"</code> quando o pai já tem altura.',
            'O <code>eager</code> desliga o <code>loading="lazy"</code>. Só vale quando o mapa já está na primeira tela — abaixo dela ele antecipa uma requisição de terceiro que talvez ninguém role para ver.',
            'O <code>title</code> é obrigatório em <code>&lt;iframe&gt;</code> para o leitor de tela dizer o que há na moldura, e o W3C cobra. Tem padrão, mas vale trocar pelo endereço real.',
            '<code>referrerpolicy="no-referrer-when-downgrade"</code> é o que a documentação do embed do Google pede; o padrão do navegador é mais restrito e corta o caminho que ele usa para resolver o lugar.',
            'O iframe é de terceiro e só carrega quando entra na viewport, pelo <code>loading="lazy"</code>. O <code>x-ui.cookie-consent</code> do projeto é informativo e não barra carregamento nenhum — o mapa não passa por ele.',
        ],
    ],
    [
        'name'        => 'sidebar',
        'title'       => 'Sidebar',
        'description' => 'Trilho de índice que acompanha a leitura. Ele gruda abaixo da navbar e marca a seção em que a pessoa está enquanto ela rola.',
        'sources'     => ['sidebar'],
        'examples'    => [
            [
                'title' => 'Índice a partir das âncoras da página',
                'code'  => <<<'BLADE'
                    <x-ui.sidebar
                        label="Seções do exemplo"
                        :items="[
                            'sidebar' => 'Sidebar',
                            'video' => 'Video',
                            'map' => 'Map',
                        ]"
                        :sticky="false"
                        class="py-0"
                    />
                    BLADE,
            ],
            [
                'title' => 'Mesma forma do config: label e url',
                'code'  => <<<'BLADE'
                    <x-ui.sidebar
                        label="Atalhos"
                        title="Ir para"
                        :items="[
                            ['label' => 'Início', 'url' => '/'],
                            ['label' => 'Política de privacidade', 'url' => '/politica-de-privacidade'],
                        ]"
                        :sticky="false"
                        class="py-0"
                    />
                    BLADE,
            ],
            [
                'title' => 'Sem o rótulo de cima',
                'code'  => <<<'BLADE'
                    <x-ui.sidebar
                        label="Seções sem título"
                        title=""
                        :items="['video' => 'Video', 'map' => 'Map']"
                        :sticky="false"
                        class="py-0"
                    />
                    BLADE,
            ],
        ],
        'notes' => [
            'Cada item também aceita <code>route</code> no lugar de <code>url</code>, igual ao <code>x-ui.menu</code> e pelo mesmo motivo: nome de rota sobrevive a mudança de caminho.',
            'O <code>items</code> aceita duas formas: mapa de âncora para rótulo (<code>[\'cookies\' => \'Cookies\']</code>), que é o que uma página com seções já tem na mão, ou lista de <code>[\'label\' => ..., \'url\' => ...]</code>, a mesma forma do <code>config(\'goognet-ui.menu\')</code> e do <code>x-ui.menu</code>.',
            'A marcação da seção atual só liga quando todos os itens são âncoras da própria página. Lista de URLs é navegação entre páginas, e ali quem manda é o endereço, não o scroll.',
            'O item atual ganha <code>aria-current="location"</code>, não <code>page</code>: ele aponta para um lugar dentro desta página, não para outra página.',
            'O <code>sticky</code> usa <code>--navbar-height</code>, publicado em tempo de execução pelo <code>resources/js/navbar.js</code> — a altura da barra muda conforme o site preencha ou não a faixa de informações. Com um valor fixo, o topo do trilho ficava atrás do cabeçalho.',
            'Fixado, o trilho é limitado à altura da tela e rola por dentro. Trilho fixo mais alto que a janela não tem como alcançar o próprio pé: a página rola, ele não acompanha, e os últimos itens ficam permanentemente abaixo da dobra. Medido no catálogo, a lista passou da tela no 22º componente e o último ficou 76px fora de alcance.',
            'A rolagem fica na lista, não no trilho inteiro: o rótulo de cima fica parado e só os itens andam, que é o que avisa a pessoa de que tem mais coisa ali.',
            'O item marcado é trazido para dentro da vista do trilho quando ele rola por dentro — senão numa lista longa a marcação acontece fora da tela, num trilho que está bem ali.',
            'A seção atual é a última que já <em>chegou ao lugar onde a âncora dela estaciona</em>, não a que está visível. Três seções cabem na tela ao mesmo tempo, e marcar "visível" faz a marcação piscar entre elas.',
            'A linha de comparação sai do <code>scroll-margin-top</code> de cada seção, não de um número escolhido a dedo. Medido: com 104px fixos contra as seções da política, que param em 112px, toda entrada acendia uma atrás do leitor.',
            'Clicar acende a entrada clicada na hora e segura até a página parar de andar. Sem isso, o scroll suave leva ~1,6s e a marcação caminha por todas as seções do caminho — o item clicado só acende no fim, o que se lê como o trilho marcando o item errado. A trava solta no <code>scrollend</code>, com um temporizador de reserva para navegador que não dispara esse evento.',
            'Sem JavaScript o trilho continua funcionando: são âncoras comuns. O que se perde é só a marcação de onde a pessoa está.',
            'Para tirar o rótulo de cima use <code>title=""</code>, não <code>:title="null"</code>. O Blade compila os padrões de <code>@props</code> como <code>$$__key = $$__key ?? $__value</code>, então passar <code>null</code> de propósito cai de volta no padrão — string vazia é o único valor que limpa um. Vale para qualquer componente da lib.',
            'O <code>label</code> é obrigatório porque uma página costuma ter várias navegações, e o leitor de tela as lista por esse nome — "navegação, navegação, navegação" não diz qual abrir.',
        ],
    ],
    [
        'name'        => 'video',
        'title'       => 'Video',
        'description' => 'Pôster clicável de um vídeo do YouTube, que abre no lightbox. Nada do YouTube carrega até o clique: a página só busca a imagem de capa.',
        'sources'     => ['video'],
        'examples'    => [
            [
                'title' => 'Vídeo pelo link normal',
                'code'  => <<<'BLADE'
                    <x-ui.video url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="max-w-xl" />
                    BLADE,
            ],
            [
                'title' => 'Link curto, com título na capa',
                'code'  => <<<'BLADE'
                    <x-ui.video
                        url="https://youtu.be/dQw4w9WgXcQ"
                        title="Como funciona o nosso atendimento"
                        class="max-w-xl"
                    />
                    BLADE,
            ],
            [
                'title' => 'Capa em HD que não existe: o script troca sozinho',
                'code'  => <<<'BLADE'
                    <x-ui.video
                        url="https://youtu.be/jNQXAC9IVRw"
                        title="Vídeo antigo, sem capa em HD"
                        class="max-w-xl"
                    />
                    BLADE,
            ],
            [
                'title' => 'Shorts em pé, capa mais leve',
                'code'  => <<<'BLADE'
                    <x-ui.video
                        url="https://www.youtube.com/shorts/dQw4w9WgXcQ"
                        ratio="tall"
                        quality="high"
                        class="max-w-52"
                    />
                    BLADE,
            ],
            [
                'title' => 'Sem lightbox, abrindo no YouTube',
                'code'  => <<<'BLADE'
                    <x-ui.video
                        url="dQw4w9WgXcQ"
                        :lightbox="false"
                        ratio="square"
                        eager
                        class="max-w-xs"
                    />
                    BLADE,
            ],
            [
                'title' => 'Capa própria, servida pelo projeto',
                'code'  => <<<'BLADE'
                    <x-ui.video url="https://youtu.be/dQw4w9WgXcQ" poster="https://picsum.photos/id/1015/1200/675" class="max-w-xl" />
                    BLADE,
            ],
        ],
        'notes' => [
            'O <code>url</code> aceita as seis formas que o YouTube distribui: <code>watch?v=</code>, <code>youtu.be</code>, <code>/embed/</code>, <code>/shorts/</code>, <code>/live/</code>, <code>/v/</code> e o id puro. O link curto é o que sai do botão de compartilhar, e ler só a query string deixava ele de fora.',
            'Link que não é do YouTube estoura <code>InvalidArgumentException</code>, como no <code>x-ui.modal</code> e no <code>x-ui.tabs</code>. Endereço errado é engano de quem escreveu a página, não estado de tempo de execução.',
            'Nenhuma requisição ao YouTube acontece no render. <code>Goognet\\Ui\\Support\\Youtube</code> é só manipulação de string; a descoberta de qual capa existe é feita pelo navegador, com o fallback em <code>resources/js/video.js</code>.',
            'Capas: <code>max</code> (1280x720, padrão) só existe se o upload foi HD; <code>standard</code>, <code>high</code> e <code>medium</code> sempre existem.',
            'A troca da capa que falta não escuta <code>error</code>. Medido: o 404 do <code>maxresdefault</code> vem com um JPEG cinza de 120x90 no corpo, então o navegador decodifica e dispara <code>load</code> — <code>error</code> nunca acontece. O <code>resources/js/video.js</code> olha o <code>naturalWidth</code>.',
            'A <code>high</code> é 480x360, ou seja 4:3 — vídeo 16:9 nela vem com tarja preta. O <code>object-cover</code> dentro do <code>aspect-video</code> corta as tarjas de volta.',
            'Com <code>poster</code> a capa sai do próprio projeto pelo <code>x-ui.image</code>: webp, <code>srcset</code> e nenhuma requisição a terceiro antes do clique.',
            'O véu escuro sobre a capa não é enfeite. O botão é branco, e a capa é qualquer imagem — um quadro de neve ou um quadro branco apagariam o controle.',
            'Sem pulso infinito. O botão responde ao ponteiro, como o resto da biblioteca; movimento que começa sozinho e não para é o que a WCAG 2.2.2 manda dar como desligar.',
            'O nome acessível do link é texto <code>sr-only</code>, não o <code>alt</code> da capa. A capa é decorativa (<code>alt=""</code>) porque o link já diz o que ela é — duas descrições da mesma coisa fazem o leitor de tela repetir.',
            'O fsLightbox é carregado por <code>resources/js/lightbox.js</code>, compartilhado com o <code>x-ui.carousel</code>. Ele varre o DOM uma vez, no import, então o import é único e acontece antes de o Swiper embaralhar os slides.',
        ],
    ],
    [
        'name'        => 'video-background',
        'title'       => 'Video background',
        'description' => 'Seção com vídeo de fundo, véu e conteúdo por cima. O plugin <code>videos()</code> do <code>vite.config.js</code> <strong>do site</strong> corta o master em webm e h264, e o componente aponta para as duas saídas — quem cobra a existência do arquivo é o próprio Vite.',
        'sources'     => ['video-background'],
        'examples'    => [
            [
                'title'  => 'Hero com chamada',
                'render' => false,
                'note'   => 'Sem prévia: exige um master em resources/videos, que não é versionado. Com o arquivo no lugar, este código roda como está.',
                'code'   => <<<'BLADE'
                    <x-ui.video-background src="fundo" poster="https://picsum.photos/id/1015/1200/675" class="flex items-center">
                        <x-ui.container>
                            <div class="max-w-2xl space-y-6 text-white">
                                <h1 class="text-5xl sm:text-6xl">Corte a laser e dobra sob medida</h1>

                                <p class="max-w-lg">Precisão, acabamento técnico e atendimento personalizado.</p>

                                <x-ui.button variant="primary" href="/contato">Solicite um orçamento</x-ui.button>
                            </div>
                        </x-ui.container>
                    </x-ui.video-background>
                    BLADE,
            ],
            [
                'title'  => 'Sem véu, altura própria',
                'render' => false,
                'note'   => 'Sem prévia, pelo mesmo motivo.',
                'code'   => <<<'BLADE'
                    <x-ui.video-background src="fundo" :overlay="false" height="h-96" class="flex items-end">
                        <x-ui.container>
                            <p class="pb-8 text-white">Sem véu, o texto precisa do seu próprio contraste.</p>
                        </x-ui.container>
                    </x-ui.video-background>
                    BLADE,
            ],
            [
                'title'  => 'Sem repetir ao terminar',
                'render' => false,
                'note'   => 'Sem prévia, pelo mesmo motivo.',
                'code'   => <<<'BLADE'
                    <x-ui.video-background src="fundo" :loop="false" poster="https://picsum.photos/id/1015/1200/675" height="h-96" />
                    BLADE,
            ],
        ],
        'notes' => [
            'O vídeo traz <code>muted</code>, <code>playsinline</code> e <code>autoplay</code> juntos porque os três são necessários: sem <code>muted</code> nenhum navegador autoplay; sem <code>playsinline</code> o Safari do iOS recusa tocar embutido e joga para tela cheia.',
            'O wrapper usa <code>isolate</code>. É isso que torna o z-index negativo seguro: abre um contexto de empilhamento, então o vídeo fica atrás do conteúdo <strong>desta</strong> seção e não atrás do fundo de um ancestral — que é quando ele some da tela.',
            'As duas saídas são pedidas sempre, sem checar o disco antes. A checagem existia e foi tirada de propósito: ela engolia o erro do Vite e deixava a seção preta sem dizer por quê.',
            'O poster também vai como <code>background-image</code> no wrapper, para o intervalo entre o primeiro pixel e o primeiro quadro não ser um vazio.',
            '<code>loop</code> é ligado por padrão: fundo que termina congela num quadro qualquer.',
            'Sob <code>prefers-reduced-motion: reduce</code> o vídeo é escondido e o poster assume, via <code>[data-video-background]</code> no <code>ui.css</code>. O CSS esconde mas não cancela o download — por isso o <code>preload="metadata"</code> na tag.',
            'O vídeo é decorativo: <code>aria-hidden</code> e <code>tabindex="-1"</code>. Conteúdo que precisa ser lido vai no slot.',
            'Fluxo do arquivo: você põe <code>fundo.mp4</code> em <code>resources/videos</code> e o build escreve <code>fundo.webm</code>, <code>fundo.h264.mp4</code> e <code>fundo.jpg</code> ao lado. O master <strong>não</strong> vai para o bundle — o <code>assets</code> do Vite lista só as saídas.',
            'O mp4 é reencodado, não copiado, por causa do <code>-movflags +faststart</code>: sem ele o átomo <code>moov</code> fica no fim do arquivo e o navegador só começa a tocar depois de baixar tudo. Medido num master 2560×1440: <code>moov</code> no byte 36, <code>mdat</code> no 1952.',
            'As saídas são mudas (<code>-an</code>) e capadas em 1920px de largura. Vídeo de fundo toca sempre com <code>muted</code>, e mais largura que isso o <code>object-fit: cover</code> corta fora. No mesmo master: 139,3 kB → 57,0 kB em h264 e 40,8 kB em webm.',
            'O poster é gerado do primeiro quadro <strong>só se</strong> não existir um <code>.jpg</code> ao lado — poster escolhido à mão nunca é sobrescrito.',
            'Nome errado ou arquivo fora do manifest estoura a <code>ViteException</code> padrão, na tela, apontando o que faltou — o mesmo erro que qualquer outro asset do Vite dá. Sem tratamento próprio: um erro de build tem que aparecer.',
            'O encode aparece no terminal conforme sai (<code>videos: fundo.webm 220,3 kB (12764 ms)</code>). Sem isso, um clipe de 20s deixa o Vite mudo por 17s e parece travado.',
            'Em <code>npm run dev</code> o servidor sobe sem esperar o encode; em <code>npm run build</code> ele espera, porque o manifest é escrito a partir do que está no disco.',
        ],
    ],
    [
        'name'        => 'whatsapp',
        'title'       => 'Whatsapp',
        'description' => 'Link para a conversa no WhatsApp. Número e mensagem vêm da configuração global quando não são passados.',
        'sources'     => ['whatsapp'],
        'examples'    => [
            [
                'title'  => 'Config global e sobrescrita',
                'layout' => 'row',
                'code'   => <<<'BLADE'
                    <x-ui.whatsapp>Falar no WhatsApp</x-ui.whatsapp>
                    <x-ui.whatsapp phone="5511999999999" message="Vim pela página de preços">Outro número</x-ui.whatsapp>
                    <x-ui.whatsapp title="Atendimento comercial">Com título próprio</x-ui.whatsapp>
                    BLADE,
            ],
        ],
        'notes' => [
            'Os valores padrão são <code>goognet-ui.whatsapp.number</code> e <code>goognet-ui.whatsapp.message</code>, alimentados pelo <code>.env</code>.',
        ],
    ],
];
