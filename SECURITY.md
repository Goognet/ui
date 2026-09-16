# Segurança

## Reportando uma vulnerabilidade

Não abra issue pública. Use **Security → Report a vulnerability** neste repositório, que abre um canal privado com os mantenedores.

Inclua o componente afetado, o valor de entrada que causa o problema e o HTML gerado. Respondemos em até 5 dias úteis.

## O que o pacote garante

- Todo `href`, `src` e atributo de URL passa por `Goognet\Ui\Support\SafeUrl`. Esquemas fora da lista em `goognet-ui.security` são descartados, inclusive com tab, quebra de linha ou maiúsculas no meio.
- `<iframe>` só abre `https` de hosts em `goognet-ui.security.frame_hosts`, com `sandbox` sem `allow-top-navigation`.
- Saída sem escape existe apenas em JSON-LD (com `JSON_HEX_TAG`), nas notas do catálogo e no logo do catálogo — todos conteúdo do próprio pacote.

## Fora do escopo

Atributos de evento (`onclick` e afins) passados pelo desenvolvedor na chamada do componente. Nunca repasse entrada de usuário como **nome** de atributo.
