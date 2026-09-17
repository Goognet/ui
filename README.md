# goognet/ui

Componentes Blade para Laravel e Tailwind CSS v4: navegação, avaliação, carrossel, galeria, mídia, modal e mais — acessíveis (WCAG 2.2) e com filtro de URL em todo `href` e `src`.

## Requisitos

- PHP 8.4+
- Laravel 13
- Tailwind CSS 4 com Vite

## Instalação

```bash
composer require goognet/ui
php artisan goognet-ui:install
npm install swiper fslightbox
```

O `goognet-ui:install` publica `config/goognet-ui.php`, acrescenta o import do CSS depois do `@import 'tailwindcss'` em `resources/css/app.css` e o `initUi()` em `resources/js/app.js`. Pode rodar de novo sem duplicar nada; `--force` sobrescreve o config, `--css` e `--js` apontam outros arquivos.

As cores da marca se trocam no `@theme` do site, depois do import:

```css
@theme {
    --color-primary-500: var(--color-blue-500);
}
```

## Uso

```blade
<x-ui.button variant="primary" href="/contato">Fale conosco</x-ui.button>

<x-ui.gallery :columns="['base' => 2, 'md' => 4]" lightbox="obras">
    <x-ui.gallery-item src="obra-1.jpg" alt="Fachada" />
</x-ui.gallery>
```

O prefixo `ui.` é configurável em `config/goognet-ui.php` (`'gn-'` → `<x-gn-button>`).

## Documentação

**https://goognet.github.io/ui** — todos os componentes, com exemplos funcionando e as props lidas do código. É publicada a cada versão.

Para ver uma mudança antes de publicar: `composer docs` gera a página e `composer docs:serve` abre em `http://localhost:8080`. Dentro de um site, a mesma página abre em `/dev/components` com `GOOGNET_UI_CATALOGUE=true`.

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
