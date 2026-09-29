# Video — `<x-ui.video>`

Pôster clicável de um vídeo do YouTube, que abre no lightbox. Nada do YouTube carrega até o clique: a página só busca a imagem de capa.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `url` | — |  |
| `title` | `null` |  |
| `poster` | `null` |  |
| `quality` | `'max'` |  |
| `ratio` | `'video'` |  |
| `lightbox` | `true` |  |
| `eager` | `false` |  |

## Exemplos

### Vídeo pelo link normal

```blade
<x-ui.video url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="max-w-xl" />
```

### Link curto, com título na capa

```blade
<x-ui.video
    url="https://youtu.be/dQw4w9WgXcQ"
    title="Como funciona o nosso atendimento"
    class="max-w-xl"
/>
```

### Capa em HD que não existe: o script troca sozinho

```blade
<x-ui.video
    url="https://youtu.be/jNQXAC9IVRw"
    title="Vídeo antigo, sem capa em HD"
    class="max-w-xl"
/>
```

### Shorts em pé, capa mais leve

```blade
<x-ui.video
    url="https://www.youtube.com/shorts/dQw4w9WgXcQ"
    ratio="tall"
    quality="high"
    class="max-w-52"
/>
```

### Sem lightbox, abrindo no YouTube

```blade
<x-ui.video
    url="dQw4w9WgXcQ"
    :lightbox="false"
    ratio="square"
    eager
    class="max-w-xs"
/>
```

### Capa própria, servida pelo projeto

```blade
<x-ui.video url="https://youtu.be/dQw4w9WgXcQ" poster="https://picsum.photos/id/1015/1200/675" class="max-w-xl" />
```

## Notas

- O `url` aceita as seis formas que o YouTube distribui: `watch?v=`, `youtu.be`, `/embed/`, `/shorts/`, `/live/`, `/v/` e o id puro. O link curto é o que sai do botão de compartilhar, e ler só a query string deixava ele de fora.
- Link que não é do YouTube estoura `InvalidArgumentException`, como no `x-ui.modal` e no `x-ui.tabs`. Endereço errado é engano de quem escreveu a página, não estado de tempo de execução.
- Nenhuma requisição ao YouTube acontece no render. `Goognet\Ui\Support\Youtube` é só manipulação de string; a descoberta de qual capa existe é feita pelo navegador, com o fallback em `resources/js/video.js`.
- Capas: `max` (1280x720, padrão) só existe se o upload foi HD; `standard`, `high` e `medium` sempre existem.
- A troca da capa que falta não escuta `error`. Medido: o 404 do `maxresdefault` vem com um JPEG cinza de 120x90 no corpo, então o navegador decodifica e dispara `load` — `error` nunca acontece. O `resources/js/video.js` olha o `naturalWidth`.
- A `high` é 480x360, ou seja 4:3 — vídeo 16:9 nela vem com tarja preta. O `object-cover` dentro do `aspect-video` corta as tarjas de volta.
- Com `poster` a capa sai do próprio projeto pelo `x-ui.image`: webp, `srcset` e nenhuma requisição a terceiro antes do clique.
- O véu escuro sobre a capa não é enfeite. O botão é branco, e a capa é qualquer imagem — um quadro de neve ou um quadro branco apagariam o controle.
- Sem pulso infinito. O botão responde ao ponteiro, como o resto da biblioteca; movimento que começa sozinho e não para é o que a WCAG 2.2.2 manda dar como desligar.
- O nome acessível do link é texto `sr-only`, não o `alt` da capa. A capa é decorativa (`alt=""`) porque o link já diz o que ela é — duas descrições da mesma coisa fazem o leitor de tela repetir.
- O fsLightbox é carregado por `resources/js/lightbox.js`, compartilhado com o `x-ui.carousel`. Ele varre o DOM uma vez, no import, então o import é único e acontece antes de o Swiper embaralhar os slides.
