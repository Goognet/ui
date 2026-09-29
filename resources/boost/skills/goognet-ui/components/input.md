# Input — `<x-ui.input>`

Campo de texto com rótulo, dica e erro. Lê sozinho a mensagem que a validação deixou e devolve o que foi digitado no envio anterior.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `name` | `null` |  |
| `id` | `null` |  |
| `type` | `'text'` |  |
| `label` | `null` |  |
| `hint` | `null` |  |
| `error` | `null` |  |
| `icon` | `null` |  |
| `value` | `null` |  |
| `mask` | `null` |  |
| `size` | `null` |  |
| `required` | `false` |  |
| `control-class` | `null` |  |

## Exemplos

### Rótulo, dica e obrigatório

```blade
<x-ui.input name="nome" label="Nome" placeholder="Como podemos te chamar?" required />
<x-ui.input name="email" type="email" label="E-mail" hint="Usamos só para responder." />
<x-ui.input name="telefone" type="tel" label="Telefone" icon="heroicon-m-phone" />
```

### Erro

```blade
<x-ui.input name="cnpj" id="cnpj-do-cliente" label="CNPJ" error="Informe um CNPJ válido." value="00.000.000/0000-00" control-class="font-mono" />
```

### Máscara

```blade
<x-ui.input name="telefone" label="Telefone" mask="phone" placeholder="(11) 90000-0000" />
<x-ui.input name="documento" label="CPF ou CNPJ" mask="cpf-cnpj" />
<x-ui.input name="valor" label="Valor" mask="money" placeholder="0,00" />
<x-ui.input name="placa" label="Placa" mask="AAA-0A00" />
```

### Tamanhos

```blade
<x-ui.input name="a" size="sm" placeholder="sm" />
<x-ui.input name="b" size="base" placeholder="base" />
<x-ui.input name="c" size="lg" placeholder="lg" />
```

## Notas

- Sem `error`, a mensagem vem do `$errors` da própria requisição — `name="items[0][qty]"` é procurado como `items.0.qty`, que é como o validator guarda. Uma mensagem passada na chamada vence a do validator.
- O erro nunca é só a borda vermelha: entra `aria-invalid`, a mensagem ganha `role="alert"` e o campo aponta para ela por `aria-describedby`.
- O campo volta preenchido com o envio anterior, exceto quando é `type="password"` — repopular devolveria a senha digitada para dentro do HTML.
- `type` aceita só os tipos de campo de texto. `file`, `submit`, `image` ou `checkbox` virariam outro controle dentro de um rótulo que promete texto, então voltam para `text`.
- `class` veste o bloco inteiro (rótulo, campo e mensagem); `control-class` veste só o campo.
- `mask` aceita um nome pronto — `phone`, `cpf`, `cnpj`, `cpf-cnpj`, `cep`, `date`, `time`, `money`, `percent`, `card` — ou um padrão escrito, onde `0` é dígito e `a` é letra.
- Junto da máscara vai o `inputmode`: no celular, campo de dígitos abre o teclado numérico em vez do alfabético.
- O padrão passado é filtrado antes de virar atributo, então um valor vindo do banco ou da query string não consegue injetar markup ali.
- `phone` e `cpf-cnpj` aceitam os dois comprimentos: a máscara acompanha o que está sendo digitado.
- A biblioteca (`imask`) é importada só quando existe campo com máscara na página. Instale com `npm install imask`.
