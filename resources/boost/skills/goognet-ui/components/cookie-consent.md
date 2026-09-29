# Cookie consent — `<x-ui.cookie-consent>`

Aviso de cookies. Não bloqueia nada: registra que o visitante foi informado e some. Renderizado no servidor, então quem já aceitou nunca recebe o markup.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `policy` | `null` |  |
| `name` | `null` |  |

## Exemplos

### Padrão

```blade
<x-ui.cookie-consent class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200" />
```

### Texto e nome do cookie próprios

```blade
<x-ui.cookie-consent name="aviso_lgpd" class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200">
    Este site usa cookies para medir audiência. Ao continuar, você concorda com a política de privacidade.
</x-ui.cookie-consent>
```

### Apontando para outra política

```blade
<x-ui.cookie-consent
    policy="/termos"
    class="static! w-full max-w-md shadow-none ring-1 ring-neutral-200"
/>
```

## Notas

- A decisão de exibir é feita no PHP, lendo o cookie. Esconder por JavaScript faria o aviso piscar em toda página, antes do script rodar.
- O cookie é escrito pelo navegador em texto puro e lido no Blade, então precisa ficar fora da criptografia de cookies do Laravel. O pacote registra essa exceção sozinho, pelo nome em `goognet-ui.cookie_consent.name`. Por isso o nome se troca **no config**: com a prop `name` diferente, a exceção não acompanha e o banner não some.
- Nada é bloqueado antes do aceite: o clique só grava `cookie_consent=accepted` por um ano, em `Path=/` e `SameSite=Lax`, e remove o card.
- Tem duas formas. Em tela estreita é uma barra colada no rodapé, em largura total: um card flutuando cem pixels acima do fundo lê como sobra de layout. De `sm` para cima vira card no canto inferior esquerdo, longe do botão flutuante do WhatsApp.
- O botão do WhatsApp sobe pela altura **real** da barra, não por um valor fixo: o script publica `--cookie-consent-height` com um `ResizeObserver`, e o `ui.css` usa isso dentro de `body:has([data-cookie-consent])`. O texto quebra em mais linhas em telas menores, então um deslocamento fixo erraria. Medido em 390px: barra de 183px, botão 12px acima dela; ao aceitar, a variável é removida e o botão volta para 12px do fundo.
- O empurrão depende de `data-floating` no elemento fixo. Outro botão flutuante que precise do mesmo tratamento é só marcar igual.
- O link "Saber mais" aponta para a rota `privacy` quando ela existe, e some quando não existe. Passe `policy` para apontar para outro lugar.
- Nos exemplos acima o `static!` tira o card do `fixed` só para ele aparecer dentro do catálogo.
