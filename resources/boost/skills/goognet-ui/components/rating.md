# Rating — `<x-ui.rating>`

Nota em estrelas. Sem `name` exibe um número; com `name` vira campo de formulário, com hover e seleção só em CSS.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `value` | `null` |  |
| `max` | `5` |  |
| `size` | `null` | `xs`, `sm`, `base`, `lg`, `xl` |
| `shape` | `null` |  |
| `label` | `null` |  |
| `clearable` | `false` |  |
| `disabled` | `false` |  |

## Exemplos

### Exibir uma nota

```blade
<x-ui.rating :value="4.5" />
<x-ui.rating :value="4.3" />
<x-ui.rating :value="3" shape="heart" />
<x-ui.rating :value="7" :max="10" size="sm" />
```

### Campo de formulário

```blade
<x-ui.rating name="atendimento" label="Como foi o atendimento?" :value="4" />
<x-ui.rating name="entrega" clearable />
<x-ui.rating name="travado" :value="3" disabled />
```

### Tamanhos

```blade
<x-ui.rating :value="4" size="xs" />
<x-ui.rating :value="4" size="sm" />
<x-ui.rating :value="4" size="base" />
<x-ui.rating :value="4" size="lg" />
<x-ui.rating :value="4" size="xl" />
```

## Notas

- `max` tem teto de 10. Cada ponto vira um SVG renderizado no servidor, e um valor sem limite derrubava a página.
- A presença de `name` é o que decide: sem ele sai um `<span role="img">` com `aria-label`; com ele sai um `<fieldset>` de radios, navegável pelo teclado como qualquer grupo de radio.
- Na exibição, o preenchimento é uma camada recortada por porcentagem, não meia estrela: `:value="4.3"` desenha 86% e lê como 4,3. Valor fora da escala é grampeado nas pontas.
- No campo, as estrelas estão no HTML de `max` para 1 e são reviradas com `flex-row-reverse`. É isso que faz o CSS puro funcionar: um input marcado só alcança os irmãos **seguintes**, então as estrelas menores precisam vir depois dele.
- Os estados moram em `[data-rating]` no `ui.css`, junto do tema do Swiper, e não em utilitárias: a prévia do hover precisa vencer a seleção atual, e utilitária sai na ordem do framework, não na ordem em que foi escrita no elemento.
- `clearable` acrescenta um radio de valor vazio, para limpar a nota enviar o campo em branco em vez de sumir do payload.
- Cada grupo gera ids próprios, então dois ratings convivem na mesma página sem um roubar o clique do outro.
