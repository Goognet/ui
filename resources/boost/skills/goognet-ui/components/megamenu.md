# Megamenu — `<x-ui.megamenu>`

Painel largo de navegação, com grupos em colunas. Vive dentro do `x-ui.menu` quando um item traz `groups`, e existe solto para quem monta o header à mão.

### Props de `<x-ui.megamenu>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `label` | `null` |  |
| `groups` | `[]` |  |
| `columns` | `null` |  |

### Props de `<x-ui.megamenu-panel>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `groups` | `[]` |  |
| `columns` | `null` |  |

## Exemplos

### O painel, aberto

```blade
<x-ui.megamenu-panel
    :columns="2"
    :groups="[
        ['label' => 'Contábil', 'children' => [
            ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase', 'description' => 'Planejamento tributário'],
            ['label' => 'Auditoria', 'url' => '/auditoria', 'icon' => 'heroicon-m-document-check', 'description' => 'Revisão de demonstrativos'],
        ]],
        ['label' => 'Fiscal', 'children' => [
            ['label' => 'Apuração', 'url' => '/apuracao', 'icon' => 'heroicon-m-calculator', 'description' => 'Mensal e trimestral'],
            ['label' => 'Obrigações', 'url' => '/obrigacoes', 'icon' => 'heroicon-m-clipboard-document-list'],
        ]],
    ]"
/>
```

### Uma coluna só

```blade
<x-ui.megamenu-panel
    :columns="1"
    :groups="[
        ['label' => 'Serviços', 'children' => [
            ['label' => 'Consultoria', 'url' => '/consultoria', 'description' => 'Planejamento tributário'],
            ['label' => 'Auditoria', 'url' => '/auditoria'],
        ]],
    ]"
/>
```

### Com gatilho, como no header

```blade
<div class="relative">
    <x-ui.megamenu
        label="Serviços"
        :columns="2"
        :groups="[
            ['label' => 'Contábil', 'children' => [
                ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase'],
            ]],
            ['label' => 'Fiscal', 'children' => [
                ['label' => 'Apuração', 'url' => '/apuracao', 'icon' => 'heroicon-m-calculator'],
            ]],
        ]"
    >
        <x-ui.button variant="primary" href="/orcamento" size="sm">Peça um orçamento</x-ui.button>
    </x-ui.megamenu>
</div>
```

## Notas

- O painel se estende sobre o ancestral posicionado mais próximo, então quem o usa solto precisa de um `relative` em volta — no `x-ui.navbar` isso já vem pronto.
- `columns` aceita 1 a 4 e as classes estão escritas por extenso no componente: nome de classe interpolado nunca entra na folha de estilo do Tailwind.
- Cada filho aceita `icon` e `description`. Sem descrição o item vira uma linha simples, e a lista continua legível.
- Abaixo de `lg` o painel não flutua: cai no fluxo, que é o que o `x-ui.menu` usa para achatá-lo dentro da gaveta.
- O slot fecha o painel com uma chamada — no header costuma ser o botão de orçamento.
