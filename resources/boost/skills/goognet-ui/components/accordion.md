# Accordion — `<x-ui.accordion>`

Sanfona sobre `<details>`/`<summary>`: o navegador já dá semântica de disclosure, Esc, Enter e busca na página. O atalho `faq` emite o JSON-LD de FAQPage.

### Props de `<x-ui.accordion>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `label` | `null` |  |
| `faq` | `[]` |  |

### Props de `<x-ui.accordion-item>`

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` *(herdada do pai)* | `null` |  |
| `label` | — |  |
| `icon` | `null` |  |
| `open` | `false` |  |

## Exemplos

### FAQ com JSON-LD

```blade
<x-ui.accordion name="docs-faq-schema" label="Perguntas frequentes" :faq="[
    'Qual o prazo de entrega?' => 'De 3 a 5 dias úteis para todo o Brasil.',
    'Posso parcelar?'          => 'Em até 12x sem juros no cartão.',
    'Tem garantia?'            => '12 meses de garantia de fábrica.',
]" />
```

### Exclusivo, com um item aberto

```blade
<x-ui.accordion name="docs-faq" label="Perguntas frequentes">
    <x-ui.accordion-item label="Qual o prazo de entrega?" open>
        <p>De 3 a 5 dias úteis para todo o Brasil.</p>
    </x-ui.accordion-item>

    <x-ui.accordion-item label="Posso parcelar?" icon="heroicon-m-credit-card">
        <p>Em até 12x sem juros no cartão.</p>
    </x-ui.accordion-item>

    <x-ui.accordion-item label="Tem garantia?">
        <p>12 meses de garantia de fábrica.</p>
    </x-ui.accordion-item>
</x-ui.accordion>
```

## Notas

- Com `name` no grupo, abrir um item fecha o outro — é o `name` nativo do `<details>`. Sem ele, cada item é independente.
- A altura anima por `::details-content` com `interpolate-size`. Navegador sem suporte abre seco, sem quebrar nada.
- O atalho `faq` emite o JSON-LD de `FAQPage` junto. A resposta é texto puro e sai escapada, porque o Google recusa markup dentro de `acceptedAnswer`. Resposta com HTML vai pelo slot, que não emite schema.
