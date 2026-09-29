# Breadcrumb — `<x-ui.breadcrumb>`

Trilha de navegação. Emite o JSON-LD de BreadcrumbList junto, para o Google entender a hierarquia.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `items` | `[]` |  |
| `separator` | `'heroicon-m-chevron-right'` |  |
| `variant` | `null` |  |
| `size` | `null` | `xs`, `sm`, `base`, `lg` |

## Exemplos

### Trilha simples

```blade
<x-ui.breadcrumb :items="[
    ['label' => 'Início', 'url' => '/'],
    ['label' => 'Serviços', 'url' => '/servicos'],
    ['label' => 'Consultoria'],
]" />
```

### Separador, ícones e tamanho

```blade
<x-ui.breadcrumb
    separator="heroicon-m-arrow-right"
    size="sm"
    :items="[
        ['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'],
        ['label' => 'Blog', 'url' => '/blog', 'icon' => 'heroicon-m-newspaper'],
        ['label' => 'Artigo'],
    ]"
/>
```

### Variantes de hover

```blade
<x-ui.breadcrumb
    variant="secondary"
    :items="[['label' => 'Início', 'url' => '/'], ['label' => 'Serviços']]"
/>
```

## Notas

- O último item nunca vira link, mesmo carregando `url`: ele recebe `aria-current="page"`.
