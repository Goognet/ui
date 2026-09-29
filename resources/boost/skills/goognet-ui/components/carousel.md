# Carousel — `<x-ui.carousel>`

Carrossel sobre o Swiper, com lightbox opcional via fslightbox. A configuração vai inteira num `data-carousel` e o script a lê por instância, então várias galerias convivem na mesma página.

### Props de `<x-ui.carousel>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `per-view` | `1` |  |
| `gap` | `16` |  |
| `autoplay` | `false` |  |
| `auto-height` | `false` |  |
| `loop` | `null` |  |
| `pagination` | `false` |  |
| `dynamic-bullets` | `false` |  |
| `navigation` | `false` |  |
| `lightbox` | `false` |  |
| `label` | `'Carrossel'` |  |

### Props de `<x-ui.carousel-slide>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `lightbox` *(herdada do pai)* | `false` |  |
| `source` | `null` |  |
| `type` | `null` |  |

## Exemplos

### Galeria responsiva com autoplay

```blade
<x-ui.carousel
    label="Galeria"
    :per-view="['base' => 2, 'md' => 3, 'lg' => 4]"
    :gap="['base' => 12, 'sm' => 20, 'md' => 24, 'lg' => 32]"
    autoplay="1800"
    pagination
>
    @foreach (range(1, 8) as $item)
        <x-ui.carousel-slide>
            <div class="flex h-40 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600">
                Item {{ $item }}
            </div>
        </x-ui.carousel-slide>
    @endforeach
</x-ui.carousel>
```

### Um por vez, com setas

```blade
<x-ui.carousel label="Depoimentos" :gap="24" navigation pagination class="px-14">
    <x-ui.carousel-slide>
        <figure class="rounded-xl border border-neutral-200 bg-white p-6 shadow-soft">
            <x-ui.rating :value="5" size="sm" />

            <blockquote class="mt-3 text-neutral-700">
                Atendimento rápido e entrega no prazo.
            </blockquote>

            <figcaption class="mt-3 text-sm text-neutral-500">Ana Prado</figcaption>
        </figure>
    </x-ui.carousel-slide>

    <x-ui.carousel-slide>
        <figure class="rounded-xl border border-neutral-200 bg-white p-6 shadow-soft">
            <x-ui.rating :value="4" size="sm" />

            <blockquote class="mt-3 text-neutral-700">
                Equipamento novo e bem conservado.
            </blockquote>

            <figcaption class="mt-3 text-sm text-neutral-500">Caio Menezes</figcaption>
        </figure>
    </x-ui.carousel-slide>
</x-ui.carousel>
```

### Altura acompanhando o slide

```blade
<x-ui.carousel label="Depoimentos" :gap="24" auto-height navigation pagination class="px-14">
    <x-ui.carousel-slide>
        <figure class="shadow-soft rounded-xl border border-neutral-200 bg-white p-6">
            <blockquote class="text-neutral-700">Resolveram em um dia.</blockquote>

            <figcaption class="mt-3 text-sm text-neutral-500">Ana Prado</figcaption>
        </figure>
    </x-ui.carousel-slide>

    <x-ui.carousel-slide>
        <figure class="shadow-soft rounded-xl border border-neutral-200 bg-white p-6">
            <blockquote class="text-neutral-700">
                Chegamos com o prazo em cima e mesmo assim refizeram o orçamento no mesmo dia.
                A equipe montou tudo em duas visitas, deixou o local limpo e ainda voltou na
                semana seguinte para conferir o acabamento. É raro encontrar esse cuidado
                depois que a nota já foi emitida.
            </blockquote>

            <figcaption class="mt-3 text-sm text-neutral-500">Caio Menezes</figcaption>
        </figure>
    </x-ui.carousel-slide>
</x-ui.carousel>
```

### Bullets dinâmicos para muitos slides

```blade
<x-ui.carousel label="Fotos" :per-view="['base' => 2, 'md' => 3]" :gap="16" dynamic-bullets>
    @foreach (range(1, 12) as $item)
        <x-ui.carousel-slide>
            <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600">
                {{ $item }}
            </div>
        </x-ui.carousel-slide>
    @endforeach
</x-ui.carousel>
```

### Galeria com lightbox

```blade
<x-ui.carousel label="Obras" :per-view="['base' => 2, 'md' => 3]" :gap="16" lightbox="obras">
    @foreach (range(1, 6) as $item)
        <x-ui.carousel-slide :source="'https://picsum.photos/id/' . ($item + 10) . '/1200/800'">
            <img
                src="https://picsum.photos/id/{{ $item + 10 }}/400/300"
                alt="Obra {{ $item }}"
                class="w-full rounded-lg"
                loading="lazy"
            />
        </x-ui.carousel-slide>
    @endforeach
</x-ui.carousel>
```

### Loop forçado e fonte de vídeo no lightbox

```blade
<x-ui.carousel label="Vídeos" :per-view="2" :gap="16" :loop="true" lightbox="videos">
    <x-ui.carousel-slide source="https://youtu.be/exemplo" type="youtube">
        <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
            Vídeo 1
        </div>
    </x-ui.carousel-slide>

    <x-ui.carousel-slide source="https://youtu.be/exemplo-2" type="youtube">
        <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
            Vídeo 2
        </div>
    </x-ui.carousel-slide>

    <x-ui.carousel-slide source="https://youtu.be/exemplo-3" type="youtube">
        <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
            Vídeo 3
        </div>
    </x-ui.carousel-slide>

    <x-ui.carousel-slide source="https://youtu.be/exemplo-4" type="youtube">
        <div class="flex h-32 items-center justify-center rounded-lg bg-neutral-100 text-sm text-neutral-600">
            Vídeo 4
        </div>
    </x-ui.carousel-slide>
</x-ui.carousel>
```

## Notas

- `perView` e `gap` aceitam valor único ou mapa por breakpoint do Tailwind (`base`, `sm`, `md`, `lg`, `xl`, `2xl`).
- O loop só liga quando há ao menos `per-view + 1` slides no maior breakpoint — a mesma conta que o Swiper faz. Abaixo disso ele não funciona e o Swiper avisa no console, então o pacote desliga em silêncio, **inclusive com `:loop="true"`**. `:loop="false"` desliga sempre. Testado contra o Swiper em 48 combinações de slides e `per-view`: nenhum aviso e nenhum loop desligado sem necessidade.
- O Swiper 12 não tem mais a opção `lazy`. Imagem preguiçosa é `loading="lazy"` no próprio `<img>`.
- Autoplay pausa no hover pelo `pauseOnMouseEnter` do Swiper, e não liga quando o sistema pede `prefers-reduced-motion: reduce`.
- `auto-height` faz a caixa acompanhar a altura do slide em exibição, em vez de todos dividirem a altura do mais alto. Vale para conteúdo de tamanho desigual — depoimento de duas linhas ao lado de um de dez. Numa grade de cartões deixe desligado: ali a altura uniforme é o que alinha a fileira.
- Com `auto-height` e `per-view` maior que 1, a altura é a do slide mais alto entre os visíveis, não a do ativo.
- A altura é animada pelo CSS do próprio Swiper. Sob `prefers-reduced-motion: reduce` o `ui.css` tira essa transição: a caixa muda de tamanho, mas sem percorrer o caminho.
- A paginação fica fora do `.swiper` de propósito: o Swiper só posiciona bullets que são filhos diretos do container, e manter fora dispensa `!important`.
- Os bullets têm área de clique de 24px (mínimo da WCAG 2.2), com o ponto visível de 12px desenhado dentro.
- `dynamic-bullets` mostra cinco pontos por vez, encolhendo os das pontas, em vez de uma fileira que cresce sem fim. Vale a partir de umas oito imagens; com poucas, só tira a noção de quantas são.
- O `dynamicBullets` do Swiper escala o próprio bullet — que aqui é o alvo de clique — e os 0,33 dele deixariam um alvo de 8px. O `ui.css` cancela esse transform e aplica a escala ao ponto: a faixa fica como o Swiper desenha e o alvo continua de 24px.
- Pedir `dynamic-bullets` já liga a paginação. Escrever os dois não é erro, mas escrever só `dynamic-bullets` também funciona — não existe o caso de pedir e não aparecer nada.
- O `lightbox` usa o pacote `fslightbox` e só entra na página que tem carrossel com ele: o import é dinâmico, num chunk à parte.
- Cada slide precisa do prop `source` com a imagem grande — o thumb fica no slot. Slide sem `source` continua slide comum, sem clique.
- O valor de `lightbox` é o nome da galeria e chega ao slide por `@aware`. Dois carrosséis com o mesmo nome viram uma galeria só; `lightbox` sem valor usa o nome `carousel` para todos.
- O fsLightbox é carregado **antes** do Swiper de propósito: ele varre o DOM na hora que entra e guarda a ordem que encontrou, e o `loop` do Swiper move os slides de lugar depois disso.
- Vídeo ou fonte que a extensão não denuncia: passe `type` no slide (`image`, `video`, `youtube`).
