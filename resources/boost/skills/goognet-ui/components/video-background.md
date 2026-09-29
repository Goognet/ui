# Video background — `<x-ui.video-background>`

Seção com vídeo de fundo, véu e conteúdo por cima. O plugin `videos()` do `vite.config.js` **do site** corta o master em webm e h264, e o componente aponta para as duas saídas — quem cobra a existência do arquivo é o próprio Vite.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `src` | — |  |
| `poster` | `null` |  |
| `loop` | `true` |  |
| `overlay` | `'bg-neutral-950/75'` |  |
| `height` | `'h-svh'` |  |

## Exemplos

### Hero com chamada

```blade
<x-ui.video-background src="fundo" poster="https://picsum.photos/id/1015/1200/675" class="flex items-center">
    <x-ui.container>
        <div class="max-w-2xl space-y-6 text-white">
            <h1 class="text-5xl sm:text-6xl">Corte a laser e dobra sob medida</h1>

            <p class="max-w-lg">Precisão, acabamento técnico e atendimento personalizado.</p>

            <x-ui.button variant="primary" href="/contato">Solicite um orçamento</x-ui.button>
        </div>
    </x-ui.container>
</x-ui.video-background>
```

### Sem véu, altura própria

```blade
<x-ui.video-background src="fundo" :overlay="false" height="h-96" class="flex items-end">
    <x-ui.container>
        <p class="pb-8 text-white">Sem véu, o texto precisa do seu próprio contraste.</p>
    </x-ui.container>
</x-ui.video-background>
```

### Sem repetir ao terminar

```blade
<x-ui.video-background src="fundo" :loop="false" poster="https://picsum.photos/id/1015/1200/675" height="h-96" />
```

## Notas

- O vídeo traz `muted`, `playsinline` e `autoplay` juntos porque os três são necessários: sem `muted` nenhum navegador autoplay; sem `playsinline` o Safari do iOS recusa tocar embutido e joga para tela cheia.
- O wrapper usa `isolate`. É isso que torna o z-index negativo seguro: abre um contexto de empilhamento, então o vídeo fica atrás do conteúdo **desta** seção e não atrás do fundo de um ancestral — que é quando ele some da tela.
- As duas saídas são pedidas sempre, sem checar o disco antes. A checagem existia e foi tirada de propósito: ela engolia o erro do Vite e deixava a seção preta sem dizer por quê.
- O poster também vai como `background-image` no wrapper, para o intervalo entre o primeiro pixel e o primeiro quadro não ser um vazio.
- `loop` é ligado por padrão: fundo que termina congela num quadro qualquer.
- Sob `prefers-reduced-motion: reduce` o vídeo é escondido e o poster assume, via `[data-video-background]` no `ui.css`. O CSS esconde mas não cancela o download — por isso o `preload="metadata"` na tag.
- O vídeo é decorativo: `aria-hidden` e `tabindex="-1"`. Conteúdo que precisa ser lido vai no slot.
- Fluxo do arquivo: você põe `fundo.mp4` em `resources/videos` e o build escreve `fundo.webm`, `fundo.h264.mp4` e `fundo.jpg` ao lado. O master **não** vai para o bundle — o `assets` do Vite lista só as saídas.
- O mp4 é reencodado, não copiado, por causa do `-movflags +faststart`: sem ele o átomo `moov` fica no fim do arquivo e o navegador só começa a tocar depois de baixar tudo. Medido num master 2560×1440: `moov` no byte 36, `mdat` no 1952.
- As saídas são mudas (`-an`) e capadas em 1920px de largura. Vídeo de fundo toca sempre com `muted`, e mais largura que isso o `object-fit: cover` corta fora. No mesmo master: 139,3 kB → 57,0 kB em h264 e 40,8 kB em webm.
- O poster é gerado do primeiro quadro **só se** não existir um `.jpg` ao lado — poster escolhido à mão nunca é sobrescrito.
- Nome errado ou arquivo fora do manifest estoura a `ViteException` padrão, na tela, apontando o que faltou — o mesmo erro que qualquer outro asset do Vite dá. Sem tratamento próprio: um erro de build tem que aparecer.
- O encode aparece no terminal conforme sai (`videos: fundo.webm 220,3 kB (12764 ms)`). Sem isso, um clipe de 20s deixa o Vite mudo por 17s e parece travado.
- Em `npm run dev` o servidor sobe sem esperar o encode; em `npm run build` ele espera, porque o manifest é escrito a partir do que está no disco.
