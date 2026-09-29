# Image — `<x-ui.image>`

Imagem responsiva. O plugin `images()` do `vite.config.js` **do site** corta cada jpg/png em 400/800/1200/1600 e em webp; o componente monta o `<picture>` a partir do que existe no disco.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `src` | — |  |
| `alt` | `''` |  |
| `widths` | `null` |  |
| `sizes` | `'100vw'` |  |
| `eager` | `false` |  |

## Exemplos

### Imagem de conteúdo

```blade
<x-ui.image src="https://picsum.photos/id/1015/1200/675" alt="Exemplo" class="w-full rounded-lg" />
```

### Vetor

```blade
<x-ui.image src="https://cdn.simpleicons.org/laravel/FF2D20" alt="Logo" class="h-10" />
```

### Imagem principal da página

```blade
<x-ui.image
    src="hero.jpg"
    alt="Exemplo"
    sizes="(min-width: 768px) 50vw, 100vw"
    class="w-full rounded-lg"
    eager
/>
```

### Restringindo as larguras

```blade
<x-ui.image src="hero.jpg" alt="Exemplo" :widths="[400, 800]" class="w-full rounded-lg" />
```

## Notas

- As prévias deste catálogo usam `picsum.photos`, e os dois últimos exemplos não têm prévia: o boilerplate é um template e não carrega foto de exemplo no disco. Como URL externa não tem cópias para escolher, ela sai como `<img>` simples — ou seja, **a prévia acima não mostra o `<picture>`**. Aponte o `src` para um jpg/png seu em `resources/images` para ver o `srcset` montado.
- Nome sem barra vive em `resources/images`; com barra, o caminho vai como veio. SVG, URL externa e arquivo sem cópias no disco saem como `<img>` simples, sem `<picture>`.
- As larguras do `srcset` vêm do que está no disco, não de uma lista fixa: o plugin não amplia imagem, então fonte de 900px gera só 400 e 800. O prop `widths` apenas **restringe** esse conjunto.
- `width` e `height` saem de `getimagesize` no arquivo de origem — é o que reserva a caixa e evita o salto de layout. Arquivo ausente, sem medida: o componente cai no `<img>` simples em vez de emitir um `<source>` quebrado.
- Padrão é `loading="lazy"`. Use `eager` só na imagem que é candidata a LCP: ela liga `fetchpriority="high"`, que perde o sentido se estiver em todas.
- `sizes` é `100vw` por padrão. Se a imagem não ocupa a largura toda, informe — o navegador escolhe o candidato por esse valor, não pelo CSS.
- Compressão: webp em `quality: 75, effort: 6` e o formato original em `quality: 80` com mozjpeg. São escalas diferentes — webp 80 sai **maior** que mozjpeg 80 na mesma foto, o que faria o navegador preferir o arquivo mais pesado pelo `<source>`. Medido numa foto de 2400px cortada em 1200: mozjpeg 80 = 168,6 kB, webp 80 = 178,6 kB, webp 75 = 140,0 kB.
- `background-image` no CSS funciona sem build: com `npm run dev`, o plugin processa a pasta ao subir e gera as cópias de arquivo novo em ~2s. Referência para arquivo que não existe em `resources/images` **quebra o build** de propósito — o padrão do Vite é só avisar e deixar o caminho quebrado ir para produção.
- As cópias com sufixo de largura são geradas e estão no `.gitignore`. O `.webp` em tamanho cheio continua versionado, porque um `.webp` pode ser arquivo de origem.
- O `x-ui.brand` tem a sua própria regra de webp: para jpg/png local ele emite o `<source>` sem conferir o disco. URL externa não recebe `<picture>` — não há irmão neste disco para apontar. São contratos diferentes, de propósito.
