# Tooltip — `<x-ui.tooltip>`

Explicação curta presa a um gatilho. Sem JavaScript: abre no hover e no foco do teclado.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `text` | `null` |  |
| `placement` | `null` |  |
| `focusable` | `false` |  |

## Exemplos

### Em volta de um controle

```blade
<x-ui.tooltip text="Copia o link desta página">
    <x-ui.button size="sm" icon="heroicon-m-link">Copiar link</x-ui.button>
</x-ui.tooltip>

<x-ui.tooltip text="Abre no WhatsApp" placement="bottom">
    <x-ui.button size="sm" variant="primary">Falar agora</x-ui.button>
</x-ui.tooltip>
```

### Em texto, que não recebe foco sozinho

```blade
<x-ui.text>
    Prazo de
    <x-ui.tooltip text="Dias úteis, contados a partir da aprovação da arte." focusable placement="top">
        <x-ui.text inline class="underline decoration-dotted">5 dias</x-ui.text>
    </x-ui.tooltip>
</x-ui.text>

<x-ui.tooltip text="À esquerda" placement="left"><x-ui.badge>left</x-ui.badge></x-ui.tooltip>
<x-ui.tooltip text="À direita" placement="right"><x-ui.badge>right</x-ui.badge></x-ui.tooltip>
```

## Notas

- Abre no `hover` e no `focus-within`: um gatilho alcançado pelo teclado nunca recebe ponteiro, e uma dica que só o mouse abre é uma dica que metade dos visitantes não vê.
- Quando o gatilho já é um botão ou um link, não é preciso mais nada. Em texto comum, `focusable` põe `tabindex="0"` e `aria-describedby` no invólucro, que é o que leva a dica ao teclado e ao leitor de tela.
- Sem `text`, o componente rende só o gatilho — nada de bolha vazia numa página gerada por laço.
- A bolha tem `pointer-events-none`: ela nunca fica entre o ponteiro e o que está embaixo.
