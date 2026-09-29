# Tabs — `<x-ui.tabs>`

Abas em CSS puro, sem JavaScript: radios escondidos guardam o estado e o painel aparece pelo seletor de irmão adjacente.

### Props de `<x-ui.tabs>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `label` | `'Abas'` |  |

### Props de `<x-ui.tab>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` *(herdada do pai)* | — |  |
| `label` | — |  |
| `icon` | `null` |  |
| `checked` | `false` |  |

## Exemplos

### Três abas, a segunda aberta

```blade
<x-ui.tabs name="docs-produto">
    <x-ui.tab label="Descrição">
        <p>Conteúdo rico, HTML à vontade.</p>
    </x-ui.tab>

    <x-ui.tab label="Ficha técnica" icon="heroicon-m-list-bullet" checked>
        <ul class="list-disc space-y-1 ps-5">
            <li>Potência: 1.500 W</li>
            <li>Tensão: 220 V</li>
            <li>Peso: 12 kg</li>
        </ul>
    </x-ui.tab>

    <x-ui.tab label="Downloads">
        <ul class="space-y-1">
            <li><x-ui.link href="#">Manual de instalação (PDF)</x-ui.link></li>
            <li><x-ui.link href="#">Ficha de segurança (PDF)</x-ui.link></li>
        </ul>
    </x-ui.tab>
</x-ui.tabs>
```

## Notas

- `name` é obrigatório: é ele que agrupa os radios. Sem ele o componente lança exceção em vez de renderizar abas que não conversam.
- Sem nenhuma aba `checked`, a primeira lidera — resolvido em CSS, com `:not(:has(:checked))`.
- As setas do teclado navegam entre as abas de graça, por serem radios. É por isso que não são botões.
- Atributos extras vão para o painel: `<x-ui.tab class="pt-10">`.
