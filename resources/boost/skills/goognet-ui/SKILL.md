---
name: goognet-ui
description: "Componentes Blade do pacote goognet/ui, as tags `<x-ui.*>` de um site Laravel com Tailwind: button, badge, navbar, menu, megamenu, footer, modal, card, table, tabs, accordion, carousel, gallery, form (input, select, textarea, checkbox, radio, field), alert, toast, tooltip, breadcrumb, pagination, sidebar, image, video, map, whatsapp, cookie-consent e outros. Use ao escrever ou revisar Blade num projeto que tem o goognet/ui instalado, ao escolher entre criar um componente e reusar um do pacote, ao personalizar aparência por token, classe ou AppServiceProvider, e ao procurar as props e os exemplos de um componente."
license: MIT
metadata:
  author: goognet
---

# goognet/ui

Os componentes vivem no pacote, não no site: não copie arquivo do `vendor` e não escreva
de novo o que já existe. Cada página abaixo traz as props lidas do código, exemplos que
rodam e as armadilhas conhecidas.

A tag é `<x-ui.nome>` por padrão. O separador faz parte do prefixo, configurável em
`goognet-ui.prefix` — `'gn-'` rende `<x-gn-button>`. O nome interno (`<x-goognet-ui::button>`)
é fixo e é como os componentes se referenciam entre si.

## Personalização, em três camadas

Nenhuma delas copia arquivo do pacote, então atualizar não apaga nada.

**Token**, no `@theme` do site, depois do import do `ui.css`. Muda a identidade inteira:
`--color-primary-*`, `--radius-control`, `--spacing-control`, `--font-weight-control`,
`--shadow-control`, `--radius-surface`, `--container-page`.

**Classe na chamada**, que substitui a do componente para a mesma propriedade em vez de
somar: `class="rounded-full h-14"` tira o `rounded-control` e o `h-control`.

**`Goognet\Ui\Ui`**, no `AppServiceProvider`, quando o mesmo desvio se repete no site:

```php
Ui::button()
    ->defaults(['variant' => 'primary'])
    ->variant('inverted', 'bg-white text-neutral-900 hover:bg-neutral-100')
    ->size('xl', 'h-14 px-8 text-lg')
    ->part('base', 'uppercase tracking-wide');
```

## Duas regras que atravessam a biblioteca

**`variant` pinta só o hover ou o preenchimento, nunca a cor semântica.** Não existe
variante `success`, `warning` ou `danger` em lugar nenhum: cor com significado sai por
classe Tailwind no ponto de uso, onde o significado está.

**Cor de marca é token, não tom numerado.** `bg-primary`, `text-primary-ink`,
`text-primary-contrast` — nunca `bg-primary-600` num componente. `-ink` é a marca numa
luminosidade legível como texto sobre fundo claro; `-contrast` é o texto que cobre um
preenchimento da marca, preto ou branco conforme a luminosidade dela.

## Componentes

| Página | Tag | O que é |
| --- | --- | --- |
| [Button](components/button.md) | `<x-ui.button>` | Botão de ação. Vira `<a>` sozinho quando recebe `href`, então serve também para call-to-action que navega. |
| [Badge](components/badge.md) | `<x-ui.badge>` | Etiqueta curta para estado, categoria ou contagem. Mesmos nomes de variante do `x-ui.button`; cor semântica sai por classe Tailwind no ponto de uso. |
| [Table](components/table.md) | `<x-ui.table>` | Tabela de dados. Rola sozinha quando não cabe, em vez de empurrar a página para o lado. |
| [Tooltip](components/tooltip.md) | `<x-ui.tooltip>` | Explicação curta presa a um gatilho. Sem JavaScript: abre no hover e no foco do teclado. |
| [Counter](components/counter.md) | `<x-ui.counter>` | Número que conta até o valor quando entra na tela. O valor final é escrito pelo servidor, então a página sem JavaScript mostra o número certo. |
| [Card](components/card.md) | `<x-ui.card>` | Bloco de conteúdo sobre uma superfície. Vira `<a>` sozinho quando recebe `href`, e só então ganha o movimento de hover. |
| [Toast](components/toast.md) | `<x-ui.toast>` | Aviso curto que aparece depois de uma ação e some sozinho. Lê o que a requisição anterior deixou na sessão, então um formulário que deu certo não precisa de mais nada. |
| [Alert](components/alert.md) | `<x-ui.alert>` | Recado na página: confirmação, erro de formulário, aviso de manutenção. Neutro por padrão, colorido no ponto de uso. |
| [Checkbox](components/checkbox.md) | `<x-ui.checkbox>` | Caixa de marcação com rótulo, dica e erro, no mesmo desenho dos demais campos. |
| [Radio](components/radio.md) | `<x-ui.radio>` | Escolha única. Mesmo desenho do checkbox, com o `value` obrigatório — é ele que diz o que a opção envia. |
| [Dropdown](components/dropdown.md) | `<x-ui.dropdown>` | Menu de ações preso a um gatilho. Usa o mesmo script do `menu`, então não traz JavaScript próprio. |
| [Input](components/input.md) | `<x-ui.input>` | Campo de texto com rótulo, dica e erro. Lê sozinho a mensagem que a validação deixou e devolve o que foi digitado no envio anterior. |
| [Textarea](components/textarea.md) | `<x-ui.textarea>` | Campo de texto longo. Mesmo rótulo, dica e erro do input, e cresce com o que é digitado. |
| [Select](components/select.md) | `<x-ui.select>` | Lista de opções. Aceita `options` como mapa, como lista ou como linhas vindas do banco. |
| [Field](components/field.md) | `<x-ui.field>` | O invólucro que o input, o textarea e o select usam por dentro: rótulo, dica e mensagem de erro amarrados ao controle. |
| [Footer](components/footer.md) | `<x-ui.footer>` | Rodapé do site: faixa de chamada, colunas de navegação e contato, e a linha legal. Tudo alimentado pelo `config/goognet-ui.php`. |
| [Image](components/image.md) | `<x-ui.image>` | Imagem responsiva. O plugin `images()` do `vite.config.js` **do site** corta cada jpg/png em 400/800/1200/1600 e em webp; o componente monta o `<picture>` a partir do que existe no disco. |
| [Rating](components/rating.md) | `<x-ui.rating>` | Nota em estrelas. Sem `name` exibe um número; com `name` vira campo de formulário, com hover e seleção só em CSS. |
| [Heading](components/heading.md) | `<x-ui.heading>` | Título. O `level` decide a semântica, o `size` decide o tamanho — os dois são separados de propósito. |
| [Text](components/text.md) | `<x-ui.text>` | Texto de corpo. Rende `<p>`, ou `<span>` com `inline` quando está dentro de uma frase. |
| [Link](components/link.md) | `<x-ui.link>` | Âncora de texto. A cor de repouso é herdada do contexto; a variante pinta só o hover, para o mesmo link servir em fundo claro e escuro. |
| [Brand](components/brand.md) | `<x-ui.brand>` | Logo do site, com nome opcional ao lado. O `logo` é o arquivo que você quer — sem convenção de nome — ou markup pelo slot de mesmo nome. |
| [Cookie consent](components/cookie-consent.md) | `<x-ui.cookie-consent>` | Aviso de cookies. Não bloqueia nada: registra que o visitante foi informado e some. Renderizado no servidor, então quem já aceitou nunca recebe o markup. |
| [Container](components/container.md) | `<x-ui.container>` | Faixa central de conteúdo, com a mesma largura máxima e o mesmo respiro lateral do resto do site. |
| [Breadcrumb](components/breadcrumb.md) | `<x-ui.breadcrumb>` | Trilha de navegação. Emite o JSON-LD de BreadcrumbList junto, para o Google entender a hierarquia. |
| [Pagination](components/pagination.md) | `<x-ui.pagination>` | Navegação entre páginas de um paginador do Laravel. Recebe o próprio `$paginator` e desenha o resumo, as páginas e as setas. |
| [Menu](components/menu.md) | `<x-ui.menu>` | Navegação principal. No desktop abre dropdown ou megamenu; abaixo de lg vira hambúrguer com gaveta e acordeão. |
| [Megamenu](components/megamenu.md) | `<x-ui.megamenu>` | Painel largo de navegação, com grupos em colunas. Vive dentro do `x-ui.menu` quando um item traz `groups`, e existe solto para quem monta o header à mão. |
| [Navbar](components/navbar.md) | `<x-ui.navbar>` | Cabeçalho do site: barra fixa opcional, faixa de contato acima e esconder-ao-rolar. |
| [Tabs](components/tabs.md) | `<x-ui.tabs>` | Abas em CSS puro, sem JavaScript: radios escondidos guardam o estado e o painel aparece pelo seletor de irmão adjacente. |
| [Accordion](components/accordion.md) | `<x-ui.accordion>` | Sanfona sobre `<details>`/`<summary>`: o navegador já dá semântica de disclosure, Esc, Enter e busca na página. O atalho `faq` emite o JSON-LD de FAQPage. |
| [Modal](components/modal.md) | `<x-ui.modal>` | Diálogo sobre `<dialog>` nativo: foco preso, Esc, fundo inerte e top layer vêm do navegador. O script só roteia os cliques. |
| [Carousel](components/carousel.md) | `<x-ui.carousel>` | Carrossel sobre o Swiper, com lightbox opcional via fslightbox. A configuração vai inteira num `data-carousel` e o script a lê por instância, então várias galerias convivem na mesma página. |
| [Gallery](components/gallery.md) | `<x-ui.gallery>` | Grade de imagens com lightbox opcional. Mesma gramática do carousel — `lightbox` no pai, `source` no item — mas sem trilho: tudo aparece de uma vez. |
| [Map](components/map.md) | `<x-ui.map>` | Mapa incorporado num `<iframe>`. O endereço sai de `goognet-ui.location.map` por padrão, então a página não repete a URL do embed. |
| [Sidebar](components/sidebar.md) | `<x-ui.sidebar>` | Trilho de índice que acompanha a leitura. Ele gruda abaixo da navbar e marca a seção em que a pessoa está enquanto ela rola. |
| [Video](components/video.md) | `<x-ui.video>` | Pôster clicável de um vídeo do YouTube, que abre no lightbox. Nada do YouTube carrega até o clique: a página só busca a imagem de capa. |
| [Video background](components/video-background.md) | `<x-ui.video-background>` | Seção com vídeo de fundo, véu e conteúdo por cima. O plugin `videos()` do `vite.config.js` **do site** corta o master em webm e h264, e o componente aponta para as duas saídas — quem cobra a existência do arquivo é o próprio Vite. |
| [Whatsapp](components/whatsapp.md) | `<x-ui.whatsapp>` | Link para a conversa no WhatsApp. Número e mensagem vêm da configuração global quando não são passados. |
