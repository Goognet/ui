# Badge — `<x-ui.badge>`

Etiqueta curta para estado, categoria ou contagem. Mesmos nomes de variante do `x-ui.button`; cor semântica sai por classe Tailwind no ponto de uso.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `variant` | `null` | `default`, `primary`, `secondary`, `outline`, `filled`, `ghost` |
| `size` | `null` | `xs`, `sm`, `base`, `lg` |
| `icon` | `null` |  |
| `icon-trailing` | `null` |  |
| `href` | `null` |  |
| `external` | `false` |  |
| `dot` | `false` |  |
| `rounded` | `null` |  |

## Exemplos

### Variantes

```blade
<x-ui.badge>Padrão</x-ui.badge>
<x-ui.badge variant="primary">Primary</x-ui.badge>
<x-ui.badge variant="secondary">Secondary</x-ui.badge>
<x-ui.badge variant="outline">Outline</x-ui.badge>
<x-ui.badge variant="filled">Filled</x-ui.badge>
<x-ui.badge variant="ghost">Ghost</x-ui.badge>
```

### Cor semântica por classe

```blade
<x-ui.badge class="bg-green-100 text-green-800" dot>Pago</x-ui.badge>
<x-ui.badge class="bg-amber-100 text-amber-800" dot>Pendente</x-ui.badge>
<x-ui.badge class="bg-red-100 text-red-800" dot>Atrasado</x-ui.badge>
<x-ui.badge class="border-2 border-neutral-300 bg-transparent text-neutral-700">Rascunho</x-ui.badge>
```

### Tamanhos, raio, ícone e link

```blade
<x-ui.badge size="xs">xs</x-ui.badge>
<x-ui.badge size="sm">sm</x-ui.badge>
<x-ui.badge size="base">base</x-ui.badge>
<x-ui.badge size="lg">lg</x-ui.badge>
<x-ui.badge rounded="md" variant="filled">rounded="md"</x-ui.badge>
<x-ui.badge variant="filled" icon="heroicon-m-check">Concluído</x-ui.badge>
<x-ui.badge href="/tags/novo" variant="primary" icon-trailing="heroicon-m-arrow-right">Novo</x-ui.badge>
```

### Etiqueta que abre em nova aba

```blade
<x-ui.badge href="https://goognet.com.br" external variant="filled">Parceiro</x-ui.badge>
```

## Notas

- Sem `href` é `<span>`; com, é `<a>` e ganha `focus-visible`. `external` sem `href` não emite `target`.
- Uma classe passada **substitui** a do variant em vez de somar: duas utilidades de fundo no mesmo elemento são decididas pela ordem na folha de estilo, não pela ordem em que foram escritas. Vale para `bg-`, `text-` e `border-`, cada um por conta própria — `class="bg-red-100"` troca só o fundo e mantém o texto do variant.
- Por isso não existe variante `success`/`warning`/`danger`: a cor semântica vem da paleta do Tailwind no ponto de uso, como no resto da lib.
- `dot` usa `bg-current`, então o ponto acompanha a cor do texto, inclusive a sobrescrita.
- `rounded` aceita um apelido (`sm`, `md`, `base`, `lg`, `xl`, `full`) ou uma utilidade inteira (`rounded-none`). Valor que não é nem um nem outro volta para a pílula, em vez de emitir uma classe que não estiliza nada.
