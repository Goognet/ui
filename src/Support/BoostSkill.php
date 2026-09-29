<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Support\Str;

/**
 * Builds the Laravel Boost skill from the catalogue, so a site that installs the package gets
 * the component documentation without the package spending every prompt on it.
 *
 * The guideline beside it is written by hand and stays short: it is inlined into the project's
 * CLAUDE.md, so everything in it is paid for on every message, whatever the conversation is
 * about. The pages below are a skill, loaded only when the components come up.
 *
 * Nothing here is a second copy of the documentation. `resources/docs/components.php` is the
 * one source, the same the published catalogue renders, and `BoostSkillTest` fails when what is
 * committed no longer matches what this builds.
 */
final class BoostSkill
{
    public const string DIRECTORY = 'resources/boost/skills/goognet-ui';

    /**
     * The skill as a map of relative path to file content.
     *
     * @return array<string, string>
     */
    public static function files(): array
    {
        $entries = Catalogue::entries();

        $files = ['SKILL.md' => self::index($entries)];

        foreach ($entries as $entry) {
            $files['components/' . $entry['name'] . '.md'] = self::page($entry);
        }

        return $files;
    }

    /**
     * @param  list<array<string, mixed>>  $entries
     */
    private static function index(array $entries): string
    {
        $rows = array_map(
            fn (array $entry): string => '| [' . $entry['title'] . '](components/' . $entry['name'] . '.md) | `<x-ui.' . $entry['name'] . '>` | ' . self::plain((string) $entry['description']) . ' |',
            $entries,
        );

        return <<<MARKDOWN
            ---
            name: goognet-ui
            description: "Componentes Blade do pacote goognet/ui, as tags `<x-ui.*>` de um site Laravel com Tailwind: button, badge, navbar, menu, megamenu, footer, modal, card, table, tabs, accordion, carousel, gallery, form (input, select, textarea, checkbox, radio, field), alert, toast, tooltip, breadcrumb, pagination, sidebar, image, video, map, whatsapp, cookie-consent e outros. Use ao escrever ou revisar Blade num projeto que tem o goognet/ui instalado, ao escolher entre criar um componente e reusar um do pacote, ao personalizar aparência por token, classe ou AppServiceProvider, e ao procurar as props e os exemplos de um componente."
            license: MIT
            metadata:
              author: goognet
            ---

            # goognet/ui

            Os componentes vivem no pacote, não no site: não copie arquivo do `vendor` e não escreva
            de novo o que já existe. Cada página abaixo traz as props lidas do código, exemplos que
            rodam e as armadilhas conhecidas.

            A tag é `<x-ui.nome>` por padrão. O separador faz parte do prefixo, configurável em
            `goognet-ui.prefix` — `'gn-'` rende `<x-gn-button>`. O nome interno (`<x-goognet-ui::button>`)
            é fixo e é como os componentes se referenciam entre si.

            ## Personalização, em três camadas

            Nenhuma delas copia arquivo do pacote, então atualizar não apaga nada.

            **Token**, no `@theme` do site, depois do import do `ui.css`. Muda a identidade inteira:
            `--color-primary-*`, `--radius-control`, `--spacing-control`, `--font-weight-control`,
            `--shadow-control`, `--radius-surface`, `--container-page`.

            **Classe na chamada**, que substitui a do componente para a mesma propriedade em vez de
            somar: `class="rounded-full h-14"` tira o `rounded-control` e o `h-control`.

            **`Goognet\Ui\Ui`**, no `AppServiceProvider`, quando o mesmo desvio se repete no site:

            ```php
            Ui::button()
                ->defaults(['variant' => 'primary'])
                ->variant('inverted', 'bg-white text-neutral-900 hover:bg-neutral-100')
                ->size('xl', 'h-14 px-8 text-lg')
                ->part('base', 'uppercase tracking-wide');
            ```

            ## Duas regras que atravessam a biblioteca

            **`variant` pinta só o hover ou o preenchimento, nunca a cor semântica.** Não existe
            variante `success`, `warning` ou `danger` em lugar nenhum: cor com significado sai por
            classe Tailwind no ponto de uso, onde o significado está.

            **Cor de marca é token, não tom numerado.** `bg-primary`, `text-primary-ink`,
            `text-primary-contrast` — nunca `bg-primary-600` num componente. `-ink` é a marca numa
            luminosidade legível como texto sobre fundo claro; `-contrast` é o texto que cobre um
            preenchimento da marca, preto ou branco conforme a luminosidade dela.

            ## Componentes

            | Página | Tag | O que é |
            | --- | --- | --- |
            MARKDOWN . "\n" . implode("\n", $rows) . "\n";
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private static function page(array $entry): string
    {
        $sections = [
            '# ' . $entry['title'] . ' — `<x-ui.' . $entry['name'] . '>`',
            self::plain((string) $entry['description']),
            self::props($entry),
            self::examples($entry),
            self::notes($entry),
        ];

        return implode("\n\n", array_filter($sections, fn (string $section): bool => $section !== '')) . "\n";
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private static function props(array $entry): string
    {
        /** @var list<string> $sources */
        $sources = $entry['sources'];

        $blocks = [];

        foreach ($sources as $source) {
            $props = ComponentProps::of($source);

            if ($props === []) {
                continue;
            }

            $options = ComponentProps::options($source);

            $rows = array_map(
                fn (array $prop): string => '| `' . $prop['name'] . ($prop['aware'] ? '` *(herdada do pai)*' : '`')
                    . ' | ' . ($prop['default'] === null ? '—' : '`' . $prop['default'] . '`')
                    . ' | ' . self::accepts($prop['name'], $options) . ' |',
                $props,
            );

            $heading = count($sources) > 1 ? '### Props de `<x-ui.' . $source . '>`' : '## Props';

            $blocks[] = $heading . "\n\n| Prop | Padrão | Aceita |\n| --- | --- | --- |\n" . implode("\n", $rows);
        }

        return implode("\n\n", $blocks);
    }

    /**
     * The names a prop accepts, when the component enumerates them. Anything else is left blank
     * rather than guessed — the examples below the table show the shape.
     *
     * @param  array{variants: list<string>, sizes: list<string>}  $options
     */
    private static function accepts(string $prop, array $options): string
    {
        $names = match ($prop) {
            'variant' => $options['variants'],
            'size'    => $options['sizes'],
            default   => [],
        };

        return $names === [] ? '' : '`' . implode('`, `', $names) . '`';
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private static function examples(array $entry): string
    {
        /** @var list<array<string, mixed>> $examples */
        $examples = $entry['examples'];

        /**
         * The code is written with the default `x-ui.` prefix and stays that way. `Catalogue::code()`
         * rewrites it to whatever the site configured, which is right for a page rendered inside a
         * site and wrong here: this is generated once, in the package, for every site at once.
         */
        $blocks = array_map(
            fn (array $example): string => '### ' . $example['title'] . "\n\n```blade\n" . trim((string) $example['code']) . "\n```",
            $examples,
        );

        return $blocks === [] ? '' : "## Exemplos\n\n" . implode("\n\n", $blocks);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private static function notes(array $entry): string
    {
        /** @var list<string> $notes */
        $notes = $entry['notes'] ?? [];

        if ($notes === []) {
            return '';
        }

        $items = array_map(fn (string $note): string => '- ' . self::plain($note), $notes);

        return "## Notas\n\n" . implode("\n", $items);
    }

    /**
     * The catalogue prose is HTML, because it is rendered into a web page. A skill is read as
     * markdown, so the tags that carry meaning are translated and the rest is dropped.
     */
    private static function plain(string $html): string
    {
        $markdown = preg_replace(
            ['~</?code>~', '~<strong>(.*?)</strong>~s', '~<em>(.*?)</em>~s'],
            ['`', '**$1**', '*$1*'],
            $html,
        ) ?? $html;

        return Str::squish(html_entity_decode(strip_tags($markdown), ENT_QUOTES | ENT_HTML5));
    }
}
