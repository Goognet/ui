# Menu — `<x-ui.menu>`

Navegação principal. No desktop abre dropdown ou megamenu; abaixo de lg vira hambúrguer com gaveta e acordeão.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `items` | `null` |  |
| `label` | `null` |  |

## Exemplos

### Links, dropdown e megamenu

```blade
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
```

### Etiqueta no item e estado decidido à mão

```blade
<x-ui.menu :items="[
    ['label' => 'Início', 'url' => '/'],
    ['label' => 'Blog', 'url' => '/blog', 'badge' => 'Novo'],
    ['label' => 'Vagas', 'url' => '/vagas', 'badge' => ['label' => '2', 'variant' => 'primary']],
    ['label' => 'Contato', 'url' => '/contato', 'current' => true],
]" />
```

## Notas

- Cada item aceita `url` (caminho literal) ou `route` (nome da rota). Prefira `route`: caminho literal em `config/goognet-ui.php` precisa ser lembrado em dois lugares, e se você mudar a rota o menu continua apontando para o endereço velho sem avisar.
- O `route` não pode ser resolvido dentro do `config/goognet-ui.php` — config é lido no bootstrap, antes de o roteador existir, e `route()` ali morre com `Argument #2 ($request) must be of type Request, null given`. Com `config:cache` seria pior: a URL ficaria congelada com o domínio da máquina que rodou o comando. Por isso o componente guarda o nome e resolve na renderização.
- Com parâmetro: `['route' => ['posts.show', ['slug' => 'meu-post']]]`. Rota inexistente estoura dizendo qual nome e qual item — funciona dentro de dropdown e megamenu também.
- O item atual acende também nas páginas abaixo dele: em `/blog/meu-artigo` o item `Blog` continua marcado. Casamento exato sozinho deixava toda página de artigo com a barra inteira apagada.
- A barra final no prefixo é o que impede `/blog` de roubar `/blog-antigo`. E a home nunca entra como prefixo: todo endereço do site começa nela, então ela acenderia em tudo.
- Passe `current` no item para decidir à mão — `true` para uma landing que pertence a uma seção sem estar abaixo dela, `false` para apagar uma seção numa página dela mesma.
- O `badge` aceita string ou array com cor: `['label' => '2', 'variant' => 'primary']`. Vai para o `x-ui.badge`, então o vocabulário é o mesmo do resto da lib, e funciona tanto em link quanto em gatilho de dropdown.
- A gaveta do celular marca a página atual como linha preenchida, não como traço. Antes ela não marcava nada, e no telefone o menu nunca dizia onde a pessoa estava.
- Um item com `children` vira dropdown; com `groups` vira megamenu. Sem os dois, é link simples.
- O slot padrão aparece só no rodapé da gaveta mobile — é onde mora o call-to-action.
