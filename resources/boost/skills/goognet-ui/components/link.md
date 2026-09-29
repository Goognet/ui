# Link — `<x-ui.link>`

Âncora de texto. A cor de repouso é herdada do contexto; a variante pinta só o hover, para o mesmo link servir em fundo claro e escuro.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `href` | `null` |  |
| `variant` | `null` | `neutral`, `primary`, `secondary`, `white`, `none` |
| `underline` | `null` |  |
| `size` | `null` | `xs`, `sm`, `base`, `lg` |
| `icon` | `null` |  |
| `icon-trailing` | `null` |  |
| `external` | `false` |  |
| `label` | `null` |  |

## Exemplos

### Variantes de hover

```blade
<x-ui.link href="#" variant="primary">primary</x-ui.link>
<x-ui.link href="#" variant="secondary">secondary</x-ui.link>
<x-ui.link href="#" variant="neutral">neutral, o padrão</x-ui.link>
<x-ui.link href="#" variant="none">sem cor no hover</x-ui.link>
```

### Hover branco, para fundo escuro

```blade
<div class="rounded-surface flex gap-6 bg-neutral-900 p-6 text-neutral-300">
    <x-ui.link href="#" variant="white">Sobre nós</x-ui.link>
    <x-ui.link href="#" variant="white" icon="heroicon-m-phone">(11) 3602-6440</x-ui.link>
</div>
```

### Sublinhado, ícone e ícone puro

```blade
<x-ui.link href="#" underline="always">sempre sublinhado</x-ui.link>
<x-ui.link href="#" underline="none">sem sublinhado</x-ui.link>
<x-ui.link href="#" icon="heroicon-m-phone">(11) 3602-6440</x-ui.link>
<x-ui.link href="#" icon-trailing="heroicon-m-arrow-top-right-on-square" external>abre em outra aba</x-ui.link>
<x-ui.link href="#" icon="ri-instagram-line" label="Instagram" />
```

### Tamanhos

```blade
<x-ui.link href="/x" size="xs">Extra pequeno</x-ui.link>
<x-ui.link href="/x" size="sm">Pequeno</x-ui.link>
<x-ui.link href="/x" size="base">Base</x-ui.link>
<x-ui.link href="/x" size="lg">Grande</x-ui.link>
```

## Notas

- Todo `href` passa por `SafeUrl`: aceita `http`, `https`, `mailto`, `tel`, âncora e caminho relativo. `javascript:` e afins — inclusive com tab ou quebra de linha no meio — são descartados e o link fica sem destino. O mesmo filtro vale para `formaction`, `xlink:href` e demais atributos repassados.
- Sem conteúdo no slot o link vira só ícone: o sublinhado some e o `label` entra como texto de leitor de tela.
- `external` (ou `target="_blank"`) já acrescenta `rel="noopener noreferrer"`.
