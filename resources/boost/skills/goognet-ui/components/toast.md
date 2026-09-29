# Toast — `<x-ui.toast>`

Aviso curto que aparece depois de uma ação e some sozinho. Lê o que a requisição anterior deixou na sessão, então um formulário que deu certo não precisa de mais nada.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `message` | `null` |  |
| `title` | `null` |  |
| `type` | `null` |  |
| `position` | `null` |  |
| `duration` | `null` |  |
| `session` | `null` |  |

## Exemplos

### No layout, uma vez

```blade
{{-- No <x-layouts.guest>, junto do rodapé: --}}
<x-ui.toast />

{{-- E no controller: --}}
{{-- return back()->with('success', 'Mensagem enviada. Respondemos no mesmo dia útil.'); --}}
```

### Direto, sem passar pela sessão

```blade
<x-ui.toast message="Orçamento salvo." title="Pronto" type="success" position="top-end" :duration="4000" />
<x-ui.toast message="Não foi possível enviar agora." type="error" session="meu-aviso" />
```

### Confirmar antes de agir

```blade
<x-ui.button
    variant="ghost"
    icon="heroicon-m-trash"
    data-confirm="Esta ação não pode ser desfeita."
    data-confirm-title="Excluir o orçamento?"
    data-confirm-action="Excluir"
>
    Excluir
</x-ui.button>
```

## Notas

- Sem mensagem nenhuma, o componente não rende nada — pode ficar no layout o tempo todo.
- As chaves lidas da sessão, nesta ordem: `toast`, `success`, `status`, `error`, `warning`, `info`. Cada nome já traz o ícone que promete; `session="minha-chave"` lê só a sua, sem supor ícone.
- O flash pode ser uma string ou um array com `message`, `title` e `type`.
- A mensagem vai como texto para a caixa, nunca como HTML: um erro de validação ou um valor vindo do banco não vira markup na tela.
- `data-confirm` em qualquer botão ou link pede confirmação antes de agir — o clique é segurado, a caixa responde, e só então a ação original acontece (formulário é enviado, o resto é clicado de novo).
- A biblioteca (`sweetalert2`) é importada no primeiro uso: página sem toast e sem confirmação não paga por ela.
