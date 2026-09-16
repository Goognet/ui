<?php

declare(strict_types = 1);

use Illuminate\View\ViewException;

const TABS = <<<'BLADE'
<x-ui.tabs name="produto">
    <x-ui.tab label="Descrição">Texto da descrição</x-ui.tab>
    <x-ui.tab label="Ficha técnica" icon="heroicon-m-list-bullet">Especificações</x-ui.tab>
    <x-ui.tab label="Downloads" checked>Manuais</x-ui.tab>
</x-ui.tabs>
BLADE;

it('groups the tabs as a labelled radio group', function (): void {
    $this->blade(TABS)
        ->assertSee('role="radiogroup"', false)
        ->assertSee('aria-label="Abas"', false);
});

it('renders one radio per tab, all sharing the group name', function (): void {
    $rendered = (string) $this->blade(TABS);

    expect(substr_count($rendered, 'type="radio"'))->toBe(3)
        ->and(substr_count($rendered, 'name="produto"'))->toBe(3);
});

it('checks only the tab marked as checked', function (): void {
    $rendered = (string) $this->blade(TABS);

    expect(preg_match_all('/<input[^>]+\schecked/', $rendered))->toBe(1)
        ->and($rendered)->toMatch('/<input[^>]+aria-controls="([^"]+)"[^>]*\schecked/');
});

it('shows the first tab when none is checked', function (): void {
    /** Entities decode back to the real selector once the browser parses the attribute. */
    $rendered = html_entity_decode((string) $this->blade(<<<'BLADE'
        <x-ui.tabs name="produto">
            <x-ui.tab label="Descrição">Texto</x-ui.tab>
        </x-ui.tabs>
    BLADE));

    expect($rendered)->not->toMatch('/<input[^>]+\schecked/')
        ->and($rendered)->toContain('[div:not(:has(:checked))>&:first-of-type]:block');
});

it('reveals a panel through the radio that sits right before it', function (): void {
    expect(html_entity_decode((string) $this->blade(TABS)))->toContain('[label:has(:checked)+&]:block');
});

it('ties every panel to the trigger that controls it', function (): void {
    $rendered = (string) $this->blade(TABS);

    preg_match_all('/<label id="(tab-[^"]+)"/', $rendered, $triggers);
    preg_match_all('/aria-labelledby="(tab-[^"]+)"/', $rendered, $panels);

    expect($triggers[1])->toHaveCount(3)
        ->and($triggers[1])->toEqual($panels[1])
        ->and(array_unique($triggers[1]))->toHaveCount(3)
        ->and(substr_count($rendered, 'aria-controls="tab-'))->toBe(3);
});

it('renders the icon of a tab that asks for one', function (): void {
    $this->blade(TABS)->assertSee('<svg', false);
});

it('merges attributes onto the panel', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.tabs name="produto" class="gap-1">
            <x-ui.tab label="Descrição" class="pt-10">Texto</x-ui.tab>
        </x-ui.tabs>
    BLADE);

    expect($rendered)->toContain('gap-1')
        ->and($rendered)->toContain('pt-10');
});

it('refuses to render tabs without a group name', function (): void {
    $this->blade('<x-ui.tabs><x-ui.tab label="Descrição">Texto</x-ui.tab></x-ui.tabs>');
})->throws(ViewException::class, 'whose name groups the radios');

it('refuses to render a tab outside of tabs', function (): void {
    $this->blade('<x-ui.tab label="Descrição">Texto</x-ui.tab>');
})->throws(ViewException::class, 'must sit inside');

it('shows a focus ring on the label, since the radio that holds focus is hidden', function (): void {
    /**
     * The state lives in an `sr-only` radio, so the library's `:focus-visible` rule paints a
     * ring nobody sees. The label wears it, and it spells the shorthand out rather than
     * leaning on `--tw-outline-style` resolving to something visible.
     */
    $rendered = (string) $this->blade('<x-ui.tabs name="t"><x-ui.tab label="Um">A</x-ui.tab></x-ui.tabs>');

    expect($rendered)->toContain('has-[:focus-visible]:[outline:2px_solid_var(--color-primary)]');
});

it('answers hover on the strip with the underline, not only with colour', function (): void {
    /** The whole language of a tab strip is the rule under the label. */
    $rendered = (string) $this->blade('<x-ui.tabs name="t"><x-ui.tab label="Um">A</x-ui.tab></x-ui.tabs>');

    expect($rendered)->toContain('hover:border-neutral-300');
});
