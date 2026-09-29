# Field — `<x-ui.field>`

O invólucro que o input, o textarea e o select usam por dentro: rótulo, dica e mensagem de erro amarrados ao controle.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `id` | `null` |  |
| `name` | `null` |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `required` | `false` |  |

## Exemplos

### Em volta de um controle próprio

```blade
<x-ui.field id="arquivo" label="Currículo" hint="PDF de até 5 MB." required error="Envie o arquivo em PDF.">
    <input id="arquivo" type="file" name="curriculo" class="text-sm text-neutral-700" />
</x-ui.field>
```

## Notas

- Serve para o controle que a lib não cobre — um `file`, um campo de terceiro — sem perder o rótulo, a dica e o erro no mesmo desenho dos demais.
- O `id` é quem amarra tudo: `for` no rótulo, `-hint` e `-error` no `aria-describedby` do controle.
