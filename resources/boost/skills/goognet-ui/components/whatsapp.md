# Whatsapp — `<x-ui.whatsapp>`

Link para a conversa no WhatsApp. Número e mensagem vêm da configuração global quando não são passados.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `phone` | `null` |  |
| `message` | `null` |  |
| `title` | `'Vamos conversar?'` |  |

## Exemplos

### Config global e sobrescrita

```blade
<x-ui.whatsapp>Falar no WhatsApp</x-ui.whatsapp>
<x-ui.whatsapp phone="5511999999999" message="Vim pela página de preços">Outro número</x-ui.whatsapp>
<x-ui.whatsapp title="Atendimento comercial">Com título próprio</x-ui.whatsapp>
```

## Notas

- Os valores padrão são `goognet-ui.whatsapp.number` e `goognet-ui.whatsapp.message`, alimentados pelo `.env`.
