# Whatsapp — `<x-ui.whatsapp>`

Link para a conversa no WhatsApp. Número e mensagem vêm da configuração global quando não são passados.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `phone` | `null` |  |
| `message` | `null` |  |
| `title` | `'Vamos conversar?'` |  |
| `as` | `null` |  |
| `variant` | `null` |  |

## Exemplos

### Config global e sobrescrita

```blade
<x-ui.whatsapp>Falar no WhatsApp</x-ui.whatsapp>
<x-ui.whatsapp phone="5511999999999" message="Vim pela página de preços">Outro número</x-ui.whatsapp>
<x-ui.whatsapp title="Atendimento comercial">Com título próprio</x-ui.whatsapp>
```

### Link ou botão

```blade
<x-ui.whatsapp>Falar no WhatsApp</x-ui.whatsapp>
<x-ui.whatsapp as="button">Falar no WhatsApp</x-ui.whatsapp>
<x-ui.whatsapp as="button" variant="outline" size="lg" icon="ri-whatsapp-line">Chamar agora</x-ui.whatsapp>
```

## Notas

- Os valores padrão são `goognet-ui.whatsapp.number` e `goognet-ui.whatsapp.message`, alimentados pelo `.env`.
- O `as` escolhe quem renderiza: `link` usa o `x-ui.link`, `button` usa o `x-ui.button`. Nos dois casos sai um `<a>`, porque o WhatsApp é navegação.
- As demais props seguem para o componente escolhido — `variant`, `size`, `icon`, `rounded` —, então valem os nomes daquele componente.
- Para o site inteiro sair como botão: `Ui::whatsapp()->defaults(['as' => 'button'])` no `AppServiceProvider`.
