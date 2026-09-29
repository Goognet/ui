# Modal — `<x-ui.modal>`

Diálogo sobre `<dialog>` nativo: foco preso, Esc, fundo inerte e top layer vêm do navegador. O script só roteia os cliques.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `title` | `null` |  |
| `size` | `null` | `sm`, `base`, `lg`, `xl`, `full` |
| `closable` | `true` |  |

## Exemplos

### Gatilho, corpo e rodapé

```blade
<x-ui.button variant="primary" data-modal-open="docs-orcamento">Pedir orçamento</x-ui.button>

<x-ui.modal name="docs-orcamento" title="Peça um orçamento">
    <p>Conte o que você precisa e respondemos em até 1 dia útil.</p>

    <x-slot:footer>
        <x-ui.button variant="ghost" data-modal-close>Cancelar</x-ui.button>
        <x-ui.button variant="primary">Enviar</x-ui.button>
    </x-slot>
</x-ui.modal>
```

### Travado: só fecha pelo botão

```blade
<x-ui.button data-modal-open="docs-aviso">Abrir aviso travado</x-ui.button>

<x-ui.modal name="docs-aviso" title="Confirme antes de sair" :closable="false" size="sm">
    <p>Esse não fecha no Esc nem no clique de fora.</p>

    <x-slot:footer>
        <x-ui.button variant="primary" data-modal-close>Entendi</x-ui.button>
    </x-slot>
</x-ui.modal>
```

## Notas

- Abre com `data-modal-open="nome"` em qualquer elemento da página; fecha com `data-modal-close` dentro do modal.
- O scroll da página trava enquanto houver modal aberto e volta quando o último fecha.
- Sem `title` e com `:closable="false"`, o cabeçalho inteiro deixa de existir.
