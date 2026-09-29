@php /** @var \Laravel\Boost\Install\GuidelineAssist $assist */ @endphp

## goognet/ui — componentes Blade

Este projeto tem uma biblioteca de componentes instalada. Antes de escrever markup novo, procure
o componente que já existe: são 43, cobrindo navegação, formulário, mídia, sobreposição e rodapé.

**Ative a skill `goognet-ui` ao trabalhar em Blade.** Ela tem uma página por componente, com as
props lidas do código, os valores que cada `variant` e `size` aceitam, exemplos que rodam e as
armadilhas conhecidas. Este bloco é só o que vale saber sempre.

A tag é `<x-ui.nome>`. O separador faz parte do prefixo, configurável em `goognet-ui.prefix`.

### Nunca copie arquivo do vendor

Personalização tem três camadas e nenhuma copia componente, então `{{ $assist->composerCommand('update') }}`
não apaga nada:

1. **Token**, no `@theme` do site, depois do import do `ui.css` — muda a identidade inteira.
2. **Classe na chamada**, que *substitui* a do componente para a mesma propriedade em vez de somar.
3. **`Goognet\Ui\Ui`** no `AppServiceProvider`, quando o mesmo desvio se repete no site.

@verbatim
<code-snippet name="Personalização por projeto" lang="php">
use Goognet\Ui\Ui;

Ui::button()
    ->defaults(['variant' => 'primary'])
    ->variant('inverted', 'bg-white text-neutral-900 hover:bg-neutral-100')
    ->part('base', 'uppercase tracking-wide');
</code-snippet>
@endverbatim

### Cor é token, não tom numerado

Use `bg-primary`, `text-primary-ink` e `text-primary-contrast`. Nunca `bg-primary-600` num
componente: a marca do site pode estar apontada para qualquer degrau da escala.

- `-ink` é a marca numa luminosidade legível **como texto sobre fundo claro**. A marca crua como
  texto reprova em contraste — lime-500 mede 1,96:1 no branco.
- `-contrast` é o texto que cobre um **preenchimento** da marca: preto ou branco, conforme a
  luminosidade dela.

### `variant` não carrega significado

Não existe variante `success`, `warning` ou `danger` em componente nenhum, de propósito. Cor com
significado sai por classe Tailwind no ponto de uso, onde o significado está: `class="bg-red-100
text-red-700"`. `variant` pinta o preenchimento da marca ou só o hover.

### Configuração

`config/goognet-ui.php` reúne prefixo das tags, esquemas e hosts liberados para URL e iframe,
dados da empresa, redes sociais, menu, WhatsApp e o catálogo local. Navbar, footer, brand, menu e
whatsapp leem dali — não repita esses dados na view.

O catálogo publicado fica em <https://goognet.github.io/ui>. Dentro do site, a mesma página abre
em `/dev/components` com `GOOGNET_UI_CATALOGUE=true`.
