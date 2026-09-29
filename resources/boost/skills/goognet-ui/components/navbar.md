# Navbar — `<x-ui.navbar>`

Cabeçalho do site: barra fixa opcional, faixa de contato acima e esconder-ao-rolar.

## Props

| Prop | Padrão | Aceita |
| --- | --- | --- |
| `position` | `null` |  |
| `auto-hide` | `false` |  |
| `border` | `true` |  |

## Exemplos

### Com faixa de informação e auto-hide

```blade
<x-ui.navbar class="bg-white" auto-hide>
    <x-slot:info class="bg-primary text-neutral-950">
        <div class="flex items-center gap-6">
            <x-ui.link href="tel:1136026440" icon="heroicon-m-phone" variant="neutral">(11) 3602-6440</x-ui.link>
        </div>

        <div class="flex items-center gap-4">
            <x-ui.link href="#" icon="ri-instagram-line" label="Instagram" variant="neutral" />
        </div>
    </x-slot>

    <x-ui.brand :href="url('/')" class="h-8 w-auto" />

    <x-ui.menu :items="[
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Contato', 'url' => '/contato'],
    ]" />
</x-ui.navbar>
```

### Estática e sem régua

```blade
<x-ui.navbar position="static" :border="false" class="bg-neutral-50">
    <x-ui.brand class="h-7 w-auto" />

    <x-ui.menu :items="[['label' => 'Início', 'url' => '/']]" />
</x-ui.navbar>
```

## Notas

- A cor vem por classe Tailwind no ponto de uso (`class="bg-neutral-900"`), nunca por token. O `bg-white` padrão sai quando você passa a sua.
- A faixa `info` é `hidden md:block` — não existe no mobile, igual à referência que o componente copia.
- O esconder usa `-top-full`, então funciona com ou sem faixa. O script ressincroniza quando a barra de URL do mobile redimensiona a viewport.
