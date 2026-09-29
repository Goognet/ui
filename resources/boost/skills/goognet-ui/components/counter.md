# Counter — `<x-ui.counter>`

Número que conta até o valor quando entra na tela. O valor final é escrito pelo servidor, então a página sem JavaScript mostra o número certo.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `value` | — |  |
| `start` | — |  |
| `duration` | `2` |  |
| `decimals` | — |  |
| `prefix` | `''` |  |
| `suffix` | `''` |  |
| `separator` | `'.'` |  |
| `decimal` | `','` |  |
| `size` | `null` | `sm`, `base`, `lg`, `xl` |

## Exemplos

### Indicadores

```blade
<div class="text-center">
    <x-ui.counter :value="1250" suffix="+" />
    <x-ui.text size="sm" class="mt-1 text-neutral-500">projetos entregues</x-ui.text>
</div>

<div class="text-center">
    <x-ui.counter :value="98.5" :decimals="1" suffix="%" size="lg" />
    <x-ui.text size="sm" class="mt-1 text-neutral-500">satisfação</x-ui.text>
</div>

<div class="text-center">
    <x-ui.counter :value="12" :start="0" :duration="3" size="sm" />
    <x-ui.text size="sm" class="mt-1 text-neutral-500">anos de casa</x-ui.text>
</div>
```

### Moeda e separadores

```blade
<x-ui.counter :value="1234567.89" :decimals="2" prefix="R$ " separator="." decimal="," size="xl" />
```

## Notas

- O número final é renderizado no servidor e o script conta a partir dele — sem JavaScript, e para um rastreador, a página mostra a figura real em vez de um zero esperando um script que nunca roda.
- A animação começa quando o elemento entra na tela, uma vez só: contador que reinicia a cada rolagem lê como defeito.
- Quem pede menos movimento no sistema (`prefers-reduced-motion`) recebe o número, sem contagem.
- A biblioteca (`countup.js`) é importada só quando existe contador na página. Instale com `npm install countup.js` — o `goognet-ui:install` avisa quando falta.
- `decimals` é limitado a 4: mais casas viram ruído, e o servidor e o script precisam concordar na formatação.
