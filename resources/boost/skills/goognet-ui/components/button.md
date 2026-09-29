# Button — `<x-ui.button>`

Botão de ação. Vira `<a>` sozinho quando recebe `href`, então serve também para call-to-action que navega.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `variant` | `null` | `default`, `primary`, `secondary`, `outline`, `filled`, `ghost` |
| `icon` | `null` |  |
| `icon-trailing` | `null` |  |
| `size` | `null` | `xs`, `sm`, `base`, `lg` |
| `href` | `null` |  |
| `external` | `false` |  |
| `type` | `'button'` |  |
| `square` | `false` |  |
| `loading` | `false` |  |
| `disabled` | `false` |  |
| `rounded` | `null` |  |

## Exemplos

### Variantes

```blade
<x-ui.button>Padrão</x-ui.button>
<x-ui.button variant="primary">Primary</x-ui.button>
<x-ui.button variant="secondary">Secondary</x-ui.button>
<x-ui.button variant="outline">Outline</x-ui.button>
<x-ui.button variant="filled">Filled</x-ui.button>
<x-ui.button variant="ghost">Ghost</x-ui.button>
```

### Tamanhos e raio

```blade
<x-ui.button size="xs">xs</x-ui.button>
<x-ui.button size="sm">sm</x-ui.button>
<x-ui.button size="base">base</x-ui.button>
<x-ui.button size="lg">lg</x-ui.button>
<x-ui.button rounded>rounded</x-ui.button>
<x-ui.button rounded="lg">rounded="lg"</x-ui.button>
```

### Ícone, quadrado, link e estados

```blade
<x-ui.button icon="heroicon-m-paper-airplane">Enviar</x-ui.button>
<x-ui.button icon-trailing="heroicon-m-arrow-right">Continuar</x-ui.button>
<x-ui.button square icon="heroicon-o-trash" variant="ghost">
    <span class="sr-only">Excluir</span>
</x-ui.button>
<x-ui.button href="/orcamento" variant="primary">Vira uma âncora</x-ui.button>
<x-ui.button loading>Carregando</x-ui.button>
<x-ui.button disabled>Desabilitado</x-ui.button>
```

### Link externo e tipo de submit

```blade
<x-ui.button href="https://goognet.com.br" external>Abre em nova aba</x-ui.button>
<x-ui.button type="submit" variant="primary">Enviar formulário</x-ui.button>
<x-ui.button type="reset" variant="ghost">Limpar</x-ui.button>
```

## Notas

- Altura, arredondamento, peso da fonte e sombra vêm de tokens (`--spacing-control`, `--radius-control`, `--font-weight-control`, `--shadow-control`). Redefina no `@theme` do site e todos os botões acompanham — veja a seção **Personalização**, no topo.
- Uma classe na chamada substitui a do componente para a mesma propriedade: `class="rounded-full h-14"` tira o `rounded-control` e o `h-control` em vez de somar a eles.
- `Ui::button()` define padrões (`defaults`), variantes e tamanhos novos, e classes por parte: `base`, `content`, `icon` e `spinner`. Um tamanho novo vale para o botão comum; o `square` segue a escala de tokens.
- As variantes `primary` e `secondary` usam `--color-primary` e `--color-secondary` — a cor da marca, sem tom numerado. A tinta por cima não é escolhida: `--color-primary-contrast` vira preto abaixo de `L 0.62` e branco acima. Nenhuma cor fixa serve para as duas pontas — preto sobre o lime mede 10,74:1 e branco 1,96:1; sobre o ciano-700 elas trocam, 3,81:1 e 5,28:1.
- A variante `outline` desenha a marca como borda. O texto dela é `text-primary-ink`, não `text-primary`, pelo motivo da nota seguinte.
- Para *texto* sobre fundo claro existe `text-primary-ink`: a mesma cor numa luminosidade legível, derivada com `oklch(from var(--color-primary) 0.45 c h)`. A cor da marca como texto mede 1,95:1 — passar o mouse num link deixava ele menos legível do que estava.
- O `ink` é derivado, não escolhido: ele acompanha qualquer cor que o site defina. Medido em seis marcas bem diferentes, incluindo amarelo (1,57 → 7,43) e ciano (1,81 → 6,33).
- Com `href` o elemento é `<a>`; sem, é `<button>` e o prop `type` passa a valer.
- `loading` e `disabled` desligam o clique nos dois casos e marcam `aria-disabled`.
