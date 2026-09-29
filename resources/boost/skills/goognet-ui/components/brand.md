# Brand — `<x-ui.brand>`

Logo do site, com nome opcional ao lado. O `logo` é o arquivo que você quer — sem convenção de nome — ou markup pelo slot de mesmo nome.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `logo` | `null` |  |
| `name` | `null` |  |
| `alt` | `null` |  |
| `href` | `null` |  |
| `external` | `false` |  |

## Exemplos

### Padrão e com link

```blade
<x-ui.brand class="h-8 w-auto" />
<x-ui.brand :href="url('/')" class="h-8 w-auto" />
```

### Arquivo ou URL, alt e link externo

```blade
<x-ui.brand logo="https://cdn.simpleicons.org/laravel/FF2D20" alt="Marca do cliente" class="h-8 w-auto" />
<x-ui.brand logo="https://cdn.simpleicons.org/vuedotjs" alt="Outra marca" class="h-8 w-auto" />
<x-ui.brand href="https://goognet.com.br" external alt="Site da agência" class="h-8 w-auto" />
```

### Símbolo com o nome ao lado

```blade
<x-ui.brand logo="https://cdn.simpleicons.org/laravel/FF2D20" name="Acme Inc." class="size-8" />
```

### Marca própria pelo slot, sem arquivo

```blade
<x-ui.brand href="/" name="Launchpad">
    <x-slot:logo class="bg-primary size-8 rounded-lg text-sm font-bold text-neutral-950">
        GN
    </x-slot:logo>
</x-ui.brand>
```

## Notas

- Sem `logo` ele usa `goognet-ui.company.logo` — o mesmo nome que alimenta o `logo` do JSON-LD, num lugar só, para o cabeçalho, o rodapé e o schema não divergirem. O boilerplate deixa essa config **vazia** e não versiona logo nenhum: sem arquivo, o componente rende o nome da empresa como letreiro, em vez de quebrar a página num caminho que não existe.
- O `logo` aponta o arquivo direto: `logo="minha-marca.svg"`. Nome sem barra procura em `resources/images`; com barra, vale como está. URL (`https://`, `//` ou `data:`) vai para o `src` como veio — é o que os exemplos acima usam, para o template não carregar logo de exemplo. Não existe sufixo nem variante a decorar.
- O `logo` é prop **e** slot, como no Flux. Escrito como atributo é caminho de arquivo; como `<x-slot:logo>` é markup — SVG inline, ícone, letra. Se vierem os dois, o slot vence.
- Com `name` o `alt` da imagem vira vazio. A palavra já está na tela; um `alt` repetindo faz o leitor de tela anunciar a empresa duas vezes seguidas. `alt` explícito continua valendo, para marca que diz algo que o nome não diz.
- O slot `logo` substitui a imagem por completo: SVG inline, ícone ou letra. As classes do slot vão para a caixa dele, então quem chama controla tamanho e cor.
- O nome não tem tamanho de fonte próprio — herda o do texto em volta. O mesmo componente lê certo numa barra de 14px e num rodapé de 18px sem prop para isso.
