# Gallery — `<x-ui.gallery>`

Grade de imagens com lightbox opcional. Mesma gramática do carousel — `lightbox` no pai, `source` no item — mas sem trilho: tudo aparece de uma vez.

### Props de `<x-ui.gallery>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `columns` | `['base' => 2, 'md' => 3]` |  |
| `gap` | `4` |  |
| `lightbox` | `false` |  |
| `label` | `null` |  |
| `masonry` | `false` |  |

### Props de `<x-ui.gallery-item>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `lightbox` *(herdada do pai)* | `false, 'masonry' => false, 'gap' => 4` |  |
| `src` | `null` |  |
| `source` | `null` |  |
| `alt` | `''` |  |
| `type` | `null` |  |
| `eager` | `false` |  |
| `sizes` | `'(min-width: 768px) 33vw, 50vw'` |  |

## Exemplos

### Mansonry, cada imagem na sua altura

```blade
<x-ui.gallery masonry lightbox :columns="['base' => 2, 'md' => 3]" :gap="4" label="Obras entregues">
    @foreach ([500, 800, 620, 900, 540, 720] as $height)
        <x-ui.gallery-item :src="'https://picsum.photos/seed/m' . $height . '/600/' . $height" alt="" />
    @endforeach
</x-ui.gallery>
```

### Grade simples

```blade
<x-ui.gallery :columns="['base' => 2, 'md' => 3]" label="Obras">
    @foreach (range(1, 6) as $item)
        <x-ui.gallery-item
            :src="'https://picsum.photos/id/' . ($item + 20) . '/600/450'"
            :alt="'Obra ' . $item"
        />
    @endforeach
</x-ui.gallery>
```

### Com lightbox

```blade
<x-ui.gallery :columns="['base' => 2, 'md' => 4]" :gap="6" lightbox="galeria-obras" label="Obras">
    @foreach (range(1, 8) as $item)
        <x-ui.gallery-item
            :src="'https://picsum.photos/id/' . ($item + 30) . '/600/450'"
            :source="'https://picsum.photos/id/' . ($item + 30) . '/1600/1200'"
            :alt="'Obra ' . $item"
        />
    @endforeach
</x-ui.gallery>
```

### Primeira imagem urgente, vídeo na grade

```blade
<x-ui.gallery :columns="['base' => 2, 'md' => 3]" lightbox="midia">
    <x-ui.gallery-item
        src="https://picsum.photos/id/40/600/450"
        source="https://picsum.photos/id/40/1600/1200"
        alt="Fachada"
        sizes="(min-width: 768px) 33vw, 50vw"
        eager
    />

    <x-ui.gallery-item
        src="https://picsum.photos/id/41/600/450"
        source="https://youtu.be/dQw4w9WgXcQ"
        type="youtube"
        alt="Tour em vídeo"
    />

    <x-ui.gallery-item src="https://picsum.photos/id/42/600/450" alt="Interior" />
</x-ui.gallery>
```

### Conteúdo próprio pelo slot

```blade
<x-ui.gallery :columns="['base' => 1, 'sm' => 3]" :gap="4">
    @foreach (['Antes', 'Durante', 'Depois'] as $fase)
        <x-ui.gallery-item>
            <figure class="shadow-soft rounded-lg border border-neutral-200 bg-white p-4">
                <div class="flex h-24 items-center justify-center rounded bg-neutral-100 text-neutral-600">
                    {{ $fase }}
                </div>

                <figcaption class="mt-2 text-sm text-neutral-500">{{ $fase }} da obra</figcaption>
            </figure>
        </x-ui.gallery-item>
    @endforeach
</x-ui.gallery>
```

## Notas

- `type` só aceita `image`, `video` ou `youtube`. Qualquer outro valor é descartado.
- `masonry` troca a grade por colunas CSS: cada imagem fica com a altura que tem, em vez de ser recortada na altura da linha. O preço é a ordem de leitura — coluna desce antes de virar, então o segundo item fica embaixo do primeiro, não ao lado.
- Na mansonry o espaço entre imagens empilhadas é a margem do próprio item (o `gap` de coluna não separa linhas), e `break-inside-avoid` impede que uma imagem seja cortada no pé da coluna.
- A galeria é `<ul>` e o item é `<li>`: leitor de tela anuncia quantas imagens são. O `label` vira `aria-label` e é opcional.
- `columns` aceita número ou mapa por breakpoint do Tailwind, de 1 a 6. `gap` aceita 2, 3, 4, 5, 6, 8, 10 ou 12 — os valores estão escritos por extenso no componente porque o scanner do Tailwind não enxerga classe montada por interpolação.
- Sem `source`, o próprio `src` abre no lightbox. Informe `source` quando existir uma versão maior — é o caso normal: o thumb não precisa ter 1600px.
- Item sem `src` e sem `source` continua item comum, sem clique. É assim que o slot livre convive com a grade clicável.
- O `source` não precisa ser imagem: com `type="youtube"` o item vira capa de vídeo dentro da mesma galeria.
- `eager` e `sizes` vão direto para o `x-ui.image`. Use `eager` só na imagem que aparece sem rolar a página.
- O nome em `lightbox` agrupa a galeria. Duas galerias com o mesmo nome viram uma só; `lightbox` sem valor usa o nome `gallery`.
- O `fslightbox` é carregado no `app.js`, antes dos carrosséis: ele varre o DOM ao entrar e guarda a ordem que encontrou. Só entra na página que tem alguma âncora `data-fslightbox`.
