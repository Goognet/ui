# Text — `<x-ui.text>`

Texto de corpo. Rende `<p>`, ou `<span>` com `inline` quando está dentro de uma frase.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `size` | `null` | `sm`, `base`, `lg`, `xl` |
| `variant` | `null` | `default`, `strong`, `subtle` |
| `inline` | `false` |  |

## Exemplos

### Tamanhos

```blade
<x-ui.text size="sm">Pequeno, para apoio.</x-ui.text>
<x-ui.text>Padrão, para corpo de texto.</x-ui.text>
<x-ui.text size="lg">Maior, para abertura de página.</x-ui.text>
<x-ui.text size="xl">Destaque.</x-ui.text>
```

### Tom

```blade
<x-ui.text variant="strong">Texto forte, para o que precisa pesar.</x-ui.text>
<x-ui.text>Texto padrão.</x-ui.text>
<x-ui.text variant="subtle">Texto discreto, para apoio.</x-ui.text>
```

### Dentro de uma frase

```blade
<x-ui.text>
    O prazo é de
    <x-ui.text variant="strong" inline>cinco dias úteis</x-ui.text>
    a partir da confirmação.
</x-ui.text>
```

## Notas

- A cor padrão só é aplicada se a classe não trouxer outra. Sem isso, `class="text-blue-700"` perdia para o cinza do componente: no CSS gerado, `blue` vem antes de `neutral`, e vence quem vem depois na folha.
- Com `inline` vira `<span>`, e perde `leading-relaxed` e `text-pretty`: espaçamento de linha e rebalanceamento das últimas linhas são de bloco, não de trecho.
- O `variant` é tom, não cor. O Flux oferece dezessete nomes de paleta aqui; esta lib mantém cor no ponto de uso — `class="text-red-700"` — para não virar uma lista de cores a manter.
- O `subtle` é `neutral-600` e não um cinza mais claro: a 16px sobre branco ele mede 7,56:1, enquanto `neutral-400` mede 2,6 e reprova nos 4,5:1 que corpo de texto deve.
