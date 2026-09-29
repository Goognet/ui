# Checkbox — `<x-ui.checkbox>`

Caixa de marcação com rótulo, dica e erro, no mesmo desenho dos demais campos.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `id` | `null` |  |
| `value` | `'1'` |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `checked` | `false` |  |
| `required` | `false` |  |

## Exemplos

### Aceite e opções

```blade
<x-ui.checkbox name="aceite" label="Li e aceito a política de privacidade" required />
<x-ui.checkbox name="novidades" value="sim" label="Quero receber novidades" hint="No máximo um e-mail por mês." checked />
<x-ui.checkbox name="termos" id="termos-2024" label="Contrato de 2024" error="É preciso aceitar para continuar." />
```

## Notas

- O id sai de `name` + `value`, então várias caixas do mesmo campo convivem sem uma roubar o clique da outra.
- A cor vem de `accent-primary`, no controle nativo: sem SVG substituto, o estado marcado continua sendo o do sistema.
