# goognet/ui

Componentes Blade para Laravel e Tailwind CSS v4: navegação, avaliação, carrossel, galeria, mídia, modal e mais — acessíveis (WCAG 2.2) e com filtro de URL em todo `href` e `src`.

## Requisitos

- PHP 8.4+
- Laravel 13
- Tailwind CSS 4 com Vite

## Instalação

```bash
composer require goognet/ui
npm install swiper fslightbox
php artisan vendor:publish --tag=goognet-ui-config
```

**CSS** — `resources/css/app.css`:

```css
@import 'tailwindcss';
@import '../../vendor/goognet/ui/resources/css/ui.css';

@theme {
    /* cores da marca: todos os componentes acompanham */
    --color-primary-500: var(--color-blue-500);
}
```

**JavaScript** — `resources/js/app.js`:

```js
import { initUi } from '../../vendor/goognet/ui/resources/js';

initUi();
```

## Uso

```blade
<x-ui.button variant="primary" href="/contato">Fale conosco</x-ui.button>

<x-ui.gallery :columns="['base' => 2, 'md' => 4]" lightbox="obras">
    <x-ui.gallery-item src="obra-1.jpg" alt="Fachada" />
</x-ui.gallery>
```

O prefixo `ui.` é configurável em `config/goognet-ui.php` (`'gn-'` → `<x-gn-button>`).

## Catálogo

Fora de produção, `/dev/components` lista todos os componentes com exemplos renderizados e props lidas do código.

## Configuração

`config/goognet-ui.php` reúne prefixo, segurança (esquemas e hosts de iframe), dados da empresa, redes sociais, menu, WhatsApp e o catálogo.

## Imagens e vídeos responsivos

`x-ui.image` e `x-ui.video-background` usam as variantes geradas no build do site (`foto-400.webp`, `video.webm`). Sem esse passo no `vite.config.js`, a imagem sai sem `srcset` e o vídeo não encontra os arquivos.

## Testes

```bash
composer test
```

## Licença

MIT. Veja [LICENSE](LICENSE) e, para reportar falhas de segurança, [SECURITY.md](SECURITY.md).
