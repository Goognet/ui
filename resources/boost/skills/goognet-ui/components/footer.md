# Footer — `<x-ui.footer>`

Rodapé do site: faixa de chamada, colunas de navegação e contato, e a linha legal. Tudo alimentado pelo `config/goognet-ui.php`.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `callout` | `true` |  |
| `callout-title` | `'Precisa de um orçamento?'` |  |
| `callout-text` | `'Resposta no mesmo dia útil.'` |  |
| `callout-action` | `'Solicite um orçamento'` |  |
| `description` | `null` |  |
| `validator` | `true` |  |

## Exemplos

### Completo

```blade
<x-ui.footer />
```

### Assinatura própria e uma coluna a mais

```blade
<x-ui.footer :callout="false">
    <div class="sm:col-span-2 lg:col-span-1">
        <x-ui.text size="sm" class="text-neutral-500">
            CNPJ 00.000.000/0001-00 — Av. Paulista, 1000, São Paulo/SP
        </x-ui.text>
    </div>

    <x-slot:credit>
        <x-ui.text size="sm">
            Feito por
            <x-ui.link href="https://goognet.com.br" external underline="hover">Goognet</x-ui.link>
        </x-ui.text>
    </x-slot:credit>
</x-ui.footer>
```

### Chamada com outro texto e sem selo

```blade
<x-ui.footer
    callout-title="Vamos conversar sobre o seu projeto?"
    callout-text="Atendemos de segunda a sexta."
    callout-action="Falar no WhatsApp"
    :validator="false"
/>
```

### Sem a faixa de chamada

```blade
<x-ui.footer :callout="false" />
```

### Com descrição própria

```blade
<x-ui.footer description="Corte a laser e dobra de chapas sob medida." :callout="false" />
```

## Notas

- A faixa de chamada vem antes dos links: quem chegou ao fim está perguntando o que fazer agora. Os textos são props (`callout-title`, `callout-text`, `callout-action`) e `:callout="false"` tira a faixa — numa política de privacidade, por exemplo. `:validator="false"` tira o selo do W3C.
- Nada é escrito à mão: navegação de `goognet-ui.menu`, redes de `goognet-ui.social`, contatos e nome de `goognet-ui.company`, assinatura de `goognet-ui.agency`. Coluna sem dado não é renderizada, em vez de sair vazia.
- O menu é **achatado**: a barra esconde uma página dentro de um dropdown, o rodapé não tem onde esconder. Todos os níveis são percorridos — `children` e `groups` — e os links sobem para uma lista só. O pai de um dropdown não entra, porque é rótulo e não destino, e um endereço alcançado duas vezes aparece uma. Item com `route` vazio não tem URL e fica fora: a barra o mostraria, o rodapé não.
- A assinatura sai de `goognet-ui.agency`, e o slot `credit` a substitui quando o crédito é um logo, outra frase ou nada disso. Sem nome na config e sem slot, a linha inteira não é renderizada.
- O slot padrão vira mais uma coluna na grade — CNPJ, endereço, selo. Conteúdo mais largo se resolve no próprio bloco, com `sm:col-span-2`.
- A marca ocupa uma faixa própria, acima de três colunas de largura igual. Como primeira coluna ela ficava com 473px para 280px de conteúdo — 233px de vão morto ao lado, porque foi dimensionada supondo uma descrição que o boilerplate não traz preenchida.
- O link da política aparece em **Institucional**, e é descartado dali se o `goognet-ui.menu` já o listar: um site que o punha na navegação principal mostrava o mesmo link duas vezes no rodapé.
- O botão da faixa é uma `<a>` vestida de botão, não um `<button>` dentro de `<a>` — conteúdo interativo aninhado é HTML inválido, e o validador do W3C acusa.
- O link para a política só aparece se a rota `privacy` existir, então o rodapé não quebra num site que ainda não tem a página.
- Os alvos das redes sociais são de 44px no mobile e 40px de `sm` para cima.
- O bloco legal são duas linhas, cada uma abrindo com o seu filete: copyright e *voltar ao topo* na primeira; selo e crédito da agência na segunda, cada um numa ponta. Numa linha só, agrupadas, elas liam como um bloco solto num canto.
- O botão flutuante do WhatsApp é fixo a 12px do canto com 64px, e a última linha é o fim da página. A folga vai **embaixo** (`pb-24`), não reservada à direita: reservar largura fazia a barra terminar antes das colunas de cima, e era o desalinhamento visível. Medido em 1280px: sem sobreposição, 22px entre a última linha e o botão.
- *Voltar ao topo* usa `href="#"`, o fragmento vazio: leva ao início do documento. O `smooth-anchors.js` suaviza e mantém a âncora fora da barra de endereço; sem o script, continua funcionando, só que instantâneo.
- O selo do **W3C Validator** aponta para a **página em que está**, não para a raiz do site: `url()->current()`, que já descarta a query string — parâmetro de rastreio não faz parte do que se valida e quebraria a busca do validador. O `rel` leva `nofollow`, porque selo de saída não deve passar ranking.
