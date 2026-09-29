# Alert — `<x-ui.alert>`

Recado na página: confirmação, erro de formulário, aviso de manutenção. Neutro por padrão, colorido no ponto de uso.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `title` | `null` |  |
| `icon` | `null` |  |
| `variant` | `null` | `default`, `filled`, `ghost` |
| `dismissible` | `false` |  |
| `live` | `false` |  |

## Exemplos

### Cor no ponto de uso

```blade
<x-ui.alert icon="heroicon-m-check-circle" title="Mensagem enviada" live class="border-green-200 bg-green-50 text-green-800">
    Respondemos no mesmo dia útil.
</x-ui.alert>

<x-ui.alert icon="heroicon-m-exclamation-triangle" class="border-amber-200 bg-amber-50 text-amber-900">
    Revise os campos marcados antes de enviar.
</x-ui.alert>

<x-ui.alert icon="heroicon-m-information-circle" variant="filled" dismissible>
    Atendimento em horário reduzido nesta sexta.
</x-ui.alert>
```

### Variantes

```blade
<x-ui.alert>Padrão, com borda.</x-ui.alert>
<x-ui.alert variant="ghost">Ghost, sem fundo.</x-ui.alert>
```

## Notas

- Não existe variante `success`/`danger` aqui pelo mesmo motivo do badge: a cor semântica vem da paleta do Tailwind no ponto de uso, e a classe passada substitui a do variant em vez de somar.
- `live` é o que acrescenta `role="alert"`. Um aviso que já estava na página quando ela abriu não deve interromper a leitura; um que aparece depois do envio, sim.
- `dismissible` marca o bloco com `data-alert`, e o `initUi()` cuida do resto — sem ele, nenhum listener é registrado.
