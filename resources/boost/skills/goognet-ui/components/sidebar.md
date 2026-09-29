# Sidebar — `<x-ui.sidebar>`

Trilho de índice que acompanha a leitura. Ele gruda abaixo da navbar e marca a seção em que a pessoa está enquanto ela rola.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `items` | `[]` |  |
| `label` | — |  |
| `title` | `'Nesta página'` |  |
| `sticky` | `true` |  |

## Exemplos

### Índice a partir das âncoras da página

```blade
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
```

### Mesma forma do config: label e url

```blade
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
```

### Sem o rótulo de cima

```blade
<x-ui.sidebar
    label="Seções sem título"
    title=""
    :items="['video' => 'Video', 'map' => 'Map']"
    :sticky="false"
    class="py-0"
/>
```

## Notas

- Cada item também aceita `route` no lugar de `url`, igual ao `x-ui.menu` e pelo mesmo motivo: nome de rota sobrevive a mudança de caminho.
- O `items` aceita duas formas: mapa de âncora para rótulo (`['cookies' => 'Cookies']`), que é o que uma página com seções já tem na mão, ou lista de `['label' => ..., 'url' => ...]`, a mesma forma do `config('goognet-ui.menu')` e do `x-ui.menu`.
- A marcação da seção atual só liga quando todos os itens são âncoras da própria página. Lista de URLs é navegação entre páginas, e ali quem manda é o endereço, não o scroll.
- O item atual ganha `aria-current="location"`, não `page`: ele aponta para um lugar dentro desta página, não para outra página.
- O `sticky` usa `--navbar-height`, publicado em tempo de execução pelo `resources/js/navbar.js` — a altura da barra muda conforme o site preencha ou não a faixa de informações. Com um valor fixo, o topo do trilho ficava atrás do cabeçalho.
- Fixado, o trilho é limitado à altura da tela e rola por dentro. Trilho fixo mais alto que a janela não tem como alcançar o próprio pé: a página rola, ele não acompanha, e os últimos itens ficam permanentemente abaixo da dobra. Medido no catálogo, a lista passou da tela no 22º componente e o último ficou 76px fora de alcance.
- A rolagem fica na lista, não no trilho inteiro: o rótulo de cima fica parado e só os itens andam, que é o que avisa a pessoa de que tem mais coisa ali.
- O item marcado é trazido para dentro da vista do trilho quando ele rola por dentro — senão numa lista longa a marcação acontece fora da tela, num trilho que está bem ali.
- A seção atual é a última que já *chegou ao lugar onde a âncora dela estaciona*, não a que está visível. Três seções cabem na tela ao mesmo tempo, e marcar "visível" faz a marcação piscar entre elas.
- A linha de comparação sai do `scroll-margin-top` de cada seção, não de um número escolhido a dedo. Medido: com 104px fixos contra as seções da política, que param em 112px, toda entrada acendia uma atrás do leitor.
- Clicar acende a entrada clicada na hora e segura até a página parar de andar. Sem isso, o scroll suave leva ~1,6s e a marcação caminha por todas as seções do caminho — o item clicado só acende no fim, o que se lê como o trilho marcando o item errado. A trava solta no `scrollend`, com um temporizador de reserva para navegador que não dispara esse evento.
- Sem JavaScript o trilho continua funcionando: são âncoras comuns. O que se perde é só a marcação de onde a pessoa está.
- Para tirar o rótulo de cima use `title=""`, não `:title="null"`. O Blade compila os padrões de `@props` como `$$__key = $$__key ?? $__value`, então passar `null` de propósito cai de volta no padrão — string vazia é o único valor que limpa um. Vale para qualquer componente da lib.
- O `label` é obrigatório porque uma página costuma ter várias navegações, e o leitor de tela as lista por esse nome — "navegação, navegação, navegação" não diz qual abrir.
