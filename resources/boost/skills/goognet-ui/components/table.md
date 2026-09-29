# Table — `<x-ui.table>`

Tabela de dados. Rola sozinha quando não cabe, em vez de empurrar a página para o lado.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `headers` | `[]` |  |
| `rows` | `[]` |  |
| `caption` | `null` |  |
| `striped` | `false` |  |
| `size` | `null` | `sm`, `base`, `lg` |

## Exemplos

### Cabeçalhos, alinhamento e zebra

```blade
<x-ui.table
    caption="Planos e preços"
    striped
    size="base"
    :headers="['Plano', 'Inclui', ['label' => 'Preço', 'align' => 'end']]"
    :rows="[
        ['Lite', 'Site institucional', 'R$ 90/mês'],
        ['Pro', 'Site + blog + suporte', 'R$ 190/mês'],
        ['Sob medida', 'Escopo fechado a cada projeto', 'sob consulta'],
    ]"
/>
```

### Linhas vindas do banco, lidas por chave

```blade
<x-ui.table
    size="sm"
    :headers="[
        ['key' => 'cidade', 'label' => 'Cidade'],
        ['key' => 'prazo', 'label' => 'Prazo', 'align' => 'end'],
    ]"
    :rows="[
        ['cidade' => 'São Paulo', 'prazo' => '2 dias'],
        ['cidade' => 'Campinas', 'prazo' => '3 dias'],
    ]"
/>
```

### Escrita à mão

```blade
<x-ui.table :headers="['Serviço', 'Situação']">
    <tr>
        <td class="px-4 py-3 text-sm">Consultoria</td>
        <td class="px-4 py-3 text-sm"><x-ui.badge>Ativo</x-ui.badge></td>
    </tr>
</x-ui.table>
```

## Notas

- A rolagem horizontal fica no invólucro da tabela, não na página: é a única exceção à regra de nunca deixar o corpo rolar para o lado.
- `headers` aceita `['Plano']`, `['plano' => 'Plano']` ou linhas com `label`, `key` e `align`. Com `key`, cada linha é lida por chave — uma coleção do banco entra sem mapear antes; sem `key`, é lida por posição.
- O alinhamento é declarado uma vez, no cabeçalho, e vale para as células daquela coluna. Preço alinhado à direita com o cabeçalho à esquerda é o erro que isso evita.
- Sem `rows`, o slot é usado como corpo — para quando uma célula precisa de markup, um badge ou um link.
- `caption` vira `<caption>` de verdade: é o que um leitor de tela anuncia antes de entrar na tabela.
