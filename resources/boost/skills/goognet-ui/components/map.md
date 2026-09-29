# Map — `<x-ui.map>`

Mapa incorporado num `<iframe>`. O endereço sai de `goognet-ui.location.map` por padrão, então a página não repete a URL do embed.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `src` | `null` |  |
| `title` | `null` |  |
| `ratio` | `'video'` |  |
| `eager` | `false` |  |

## Exemplos

### Mapa da configuração

```blade
<x-ui.map src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik" />
```

### Proporção e título próprios

```blade
<x-ui.map
    src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
    title="Onde fica a loja da Avenida Paulista"
    ratio="wide"
    class="rounded-xl"
/>
```

### Mapa acima da dobra

```blade
<x-ui.map
    src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
    eager
    ratio="square"
/>
```

### Altura vinda do pai

```blade
<div class="h-64">
    <x-ui.map
        src="https://www.openstreetmap.org/export/embed.html?bbox=-46.67%2C-23.57%2C-46.64%2C-23.55&amp;layer=mapnik"
        :ratio="false"
        class="size-full"
    />
</div>
```

## Notas

- O iframe leva `sandbox` sem `allow-top-navigation`: um clique dentro do mapa não consegue levar a página inteira para outro endereço. Medido com Google e OSM — renderizam igual e o "Abrir no Maps" continua abrindo nova aba.
- Só abre hosts de `goognet-ui.security.frame_hosts` (Google Maps e OpenStreetMap por padrão) e só em `https`. Um endereço vindo de painel não vira página de outro site dentro do seu.
- Sem `src` ele usa `goognet-ui.location.map`, que vem de `LOCATION_MAP_LINK` no `.env`.
- A URL do Google Maps só sai do diálogo **Compartilhar → Incorporar um mapa**: é uma string opaca que começa com `/maps/embed?pb=` e não dá para escrever à mão. O formato antigo `maps.google.com/?output=embed` hoje redireciona para uma página com `X-Frame-Options: sameorigin`, que o navegador recusa enquadrar — por isso os exemplos aqui usam OpenStreetMap, que enquadra sem chave.
- Link vazio não renderiza nada. `<iframe src="">` não é quadro vazio: o navegador resolve a string vazia contra o documento atual e carrega a própria página dentro da caixa.
- O `ratio` reserva a altura antes dos tiles chegarem. O embed do Google não tem tamanho intrínseco, então sem proporção a caixa fica com altura zero e empurra a página quando termina de carregar — o layout shift que o Core Web Vitals mede.
- Valores de `ratio`: `video` (padrão), `square`, `wide`, `tall`, qualquer utility `aspect-*`, ou `:ratio="false"` quando o pai já tem altura.
- O `eager` desliga o `loading="lazy"`. Só vale quando o mapa já está na primeira tela — abaixo dela ele antecipa uma requisição de terceiro que talvez ninguém role para ver.
- O `title` é obrigatório em `<iframe>` para o leitor de tela dizer o que há na moldura, e o W3C cobra. Tem padrão, mas vale trocar pelo endereço real.
- `referrerpolicy="no-referrer-when-downgrade"` é o que a documentação do embed do Google pede; o padrão do navegador é mais restrito e corta o caminho que ele usa para resolver o lugar.
- O iframe é de terceiro e só carrega quando entra na viewport, pelo `loading="lazy"`. O `x-ui.cookie-consent` do projeto é informativo e não barra carregamento nenhum — o mapa não passa por ele.
