# Heading — `<x-ui.heading>`

Título. O `level` decide a semântica, o `size` decide o tamanho — os dois são separados de propósito.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `size` | `null` | `base`, `lg`, `xl`, `2xl` |
| `level` | `null` |  |

## Exemplos

### Os quatro tamanhos

```blade
<x-ui.heading size="base">Rótulo de campo</x-ui.heading>
<x-ui.heading size="lg">Título de card</x-ui.heading>
<x-ui.heading size="xl">Título de seção</x-ui.heading>
<x-ui.heading size="2xl">Título da página</x-ui.heading>
```

### Com nível, entra no sumário da página

```blade
<x-ui.heading :level="2" size="xl">Seção de verdade</x-ui.heading>
```

### Cor no ponto de uso

```blade
<x-ui.heading size="xl" class="text-primary-ink">Destaque da marca</x-ui.heading>
```

## Notas

- Sem `level` ele rende `<div>`, não `<h?>`. É deliberado: título de card que não é subdivisão do documento não deve entrar no sumário que o leitor de tela percorre. Quando for seção de verdade, passe `:level="2"`.
- Tamanho e nível são separados: uma `h2` pode ser pequena e um rótulo pode ser grande. Amarrar os dois obrigaria a página a escolher entre o sumário certo e a proporção certa.
- A escala é a que as páginas já usavam — `2xl` é o título da política, `xl` o de seção, `lg` o do modal, `base` um rótulo.
- Cor vem por classe: `class="text-primary-ink"`. O componente não tem prop de cor, pela mesma razão do `x-ui.badge` — cor semântica se escreve onde tem significado.
- Vale a regra de `.ai/rules/views.md`: uma `h1` por página, e `<section>` abre com `<header>` em volta do título.
