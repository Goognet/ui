# Dropdown — `<x-ui.dropdown>`

Menu de ações preso a um gatilho. Usa o mesmo script do `menu`, então não traz JavaScript próprio.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `id` | `null` |  |
| `label` | `null` |  |
| `icon` | `null` |  |
| `align` | `null` |  |
| `width` | `null` |  |
| `variant` | `null` |  |
| `size` | `null` |  |

## Exemplos

### Ações

```blade
<x-ui.dropdown label="Ações" icon="heroicon-m-ellipsis-horizontal" variant="default" size="base">
    <x-ui.link href="/editar" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Editar</x-ui.link>
    <x-ui.link href="/duplicar" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Duplicar</x-ui.link>
</x-ui.dropdown>

<x-ui.dropdown label="Alinhado à direita" align="end" width="min-w-64">
    <x-ui.link href="/relatorio" underline="none" class="block rounded-control px-3 py-2 text-sm hover:bg-neutral-50">Relatório mensal</x-ui.link>
</x-ui.dropdown>
```

### Gatilho próprio

```blade
<x-ui.dropdown id="painel-conta">
    <x-slot:trigger>
        <x-ui.button variant="ghost" data-menu-dropdown data-state="closed" aria-controls="painel-conta" aria-expanded="false">
            Minha conta
        </x-ui.button>
    </x-slot:trigger>

    <x-ui.text size="sm" class="px-3 py-2 text-neutral-600">Sessão iniciada</x-ui.text>
</x-ui.dropdown>
```

## Notas

- O painel é irmão do gatilho dentro de um `[data-menu]`, que é o que o script do menu observa: abrir, fechar no clique fora, no `Esc` e andar com as setas já vêm de lá.
- Cada dropdown gera o próprio id, então vários na mesma página não se confundem.
- Com `x-slot:trigger`, o gatilho é seu — passe `id` no dropdown e repita esse mesmo valor no `aria-controls` do gatilho: é por ele que o script encontra o painel. Mantenha também `data-menu-dropdown`, `data-state` e `aria-expanded`, que completam o contrato.
