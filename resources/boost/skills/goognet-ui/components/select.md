# Select — `<x-ui.select>`

Lista de opções. Aceita `options` como mapa, como lista ou como linhas vindas do banco.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `id` | `null` |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `options` | `[]` |  |
| `selected` | `null` |  |
| `placeholder` | `null` |  |
| `size` | `null` |  |
| `required` | `false` |  |
| `control-class` | `null` |  |

## Exemplos

### Opções e placeholder

```blade
<x-ui.select
    name="assunto"
    label="Assunto"
    placeholder="Escolha um assunto"
    :options="['orcamento' => 'Orçamento', 'suporte' => 'Suporte', 'outro' => 'Outro']"
    selected="suporte"
    hint="Responde quem cuida do assunto."
    required
/>

<x-ui.select
    name="uf"
    id="estado"
    label="Estado"
    size="sm"
    control-class="font-mono"
    error="Escolha um estado."
    :options="['sp' => 'São Paulo', 'rj' => 'Rio de Janeiro']"
/>
```

## Notas

- `:options="['sp' => 'São Paulo']"`, `:options="['São Paulo']"` e linhas com `value`/`label` (ou `id`/`name`) chegam todos na mesma forma, então uma coleção do banco entra sem mapear antes.
- `placeholder` vira uma opção de valor vazio no topo, marcada enquanto nada foi escolhido — com `required`, é ela que faz o navegador cobrar a escolha.
- A seta é um SVG por cima com `pointer-events-none`: o clique continua abrindo a lista nativa.
