# Radio — `<x-ui.radio>`

Escolha única. Mesmo desenho do checkbox, com o `value` obrigatório — é ele que diz o que a opção envia.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `id` | `null` |  |
| `value` | — |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `checked` | `false` |  |
| `required` | `false` |  |

## Exemplos

### Um grupo

```blade
<x-ui.radio name="plano" value="lite" label="Lite" checked />
<x-ui.radio name="plano" value="pro" label="Pro" hint="Inclui suporte prioritário." />
<x-ui.radio name="plano" id="plano-custom" value="sob-medida" label="Sob medida" error="Escolha um plano." required />
```

## Notas

- Um radio sem `value` enviaria `on` em qualquer opção do grupo, então o componente exige o valor em vez de escolher um por você.
- Depois de um envio recusado, volta marcada a opção que tinha sido escolhida.
