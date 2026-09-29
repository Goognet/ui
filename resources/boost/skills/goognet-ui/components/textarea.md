# Textarea — `<x-ui.textarea>`

Campo de texto longo. Mesmo rótulo, dica e erro do input, e cresce com o que é digitado.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `id` | `null` |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `rows` | `4` |  |
| `size` | `null` |  |
| `required` | `false` |  |
| `control-class` | `null` |  |

## Exemplos

### Mensagem

```blade
<x-ui.textarea name="mensagem" label="Mensagem" rows="5" placeholder="Conte o que você precisa" />
<x-ui.textarea name="obs" id="observacoes" label="Observações" hint="Opcional." error="Passou de 500 caracteres." />
<x-ui.textarea name="resumo" label="Resumo" size="sm" required control-class="font-mono" />
```

## Notas

- `field-sizing-content` faz a caixa acompanhar o texto; `rows` continua valendo como altura inicial.
- O conteúdo sai do slot; sem slot, volta o que foi enviado da última vez.
