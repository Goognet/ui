# Card — `<x-ui.card>`

Bloco de conteúdo sobre uma superfície. Vira `<a>` sozinho quando recebe `href`, e só então ganha o movimento de hover.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `href` | `null` |  |
| `external` | `false` |  |
| `variant` | `null` | `default`, `elevated`, `filled`, `ghost` |
| `padding` | `null` |  |

## Exemplos

### Variantes

```blade
<x-ui.card class="max-w-xs">Padrão, com borda.</x-ui.card>
<x-ui.card variant="elevated" class="max-w-xs">Elevated, com sombra.</x-ui.card>
<x-ui.card variant="filled" class="max-w-xs">Filled, sem borda.</x-ui.card>
<x-ui.card variant="ghost" class="max-w-xs">Ghost, só o espaçamento.</x-ui.card>
```

### Cabeçalho, rodapé e mídia

```blade
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
```

### Espaçamento

```blade
<x-ui.card padding="none" class="max-w-[10rem]">none</x-ui.card>
<x-ui.card padding="sm" class="max-w-[10rem]">sm</x-ui.card>
<x-ui.card padding="lg" class="max-w-[10rem]">lg</x-ui.card>
<x-ui.card href="https://goognet.com.br" external class="max-w-[10rem]">external</x-ui.card>
```

## Notas

- O movimento de hover só existe quando o card leva a algum lugar: movimento promete clique.
- A mídia é puxada para fora do espaçamento com margem negativa do tamanho do `padding`, então a imagem encosta na borda e acompanha o raio do topo.
- Um `href` recusado pelo filtro de URL deixa o card como `<div>` — sem `target` nem `rel` sobrando, que seriam erro de validação.
