<?php

declare(strict_types = 1);

const ACCORDION = <<<'BLADE'
<x-ui.accordion name="faq" label="Perguntas frequentes">
    <x-ui.accordion-item label="Qual o prazo?" open>Três dias úteis</x-ui.accordion-item>
    <x-ui.accordion-item label="Posso parcelar?" icon="heroicon-m-credit-card">Em até 12x</x-ui.accordion-item>
    <x-ui.accordion-item label="Tem garantia?">Doze meses</x-ui.accordion-item>
</x-ui.accordion>
BLADE;

it('renders one details block with a summary per item', function (): void {
    $rendered = (string) $this->blade(ACCORDION);

    expect(substr_count($rendered, '<details'))->toBe(3)
        ->and(substr_count($rendered, '<summary'))->toBe(3)
        ->and($rendered)->toContain('Qual o prazo?')
        ->toContain('Três dias úteis');
});

it('labels the group when a label is given', function (): void {
    $this->blade(ACCORDION)
        ->assertSee('role="group"', false)
        ->assertSee('aria-label="Perguntas frequentes"', false);
});

it('shares the group name so only one item stays open', function (): void {
    expect(substr_count((string) $this->blade(ACCORDION), 'name="faq"'))->toBe(3);
});

it('leaves the items independent when the group has no name', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.accordion>
            <x-ui.accordion-item label="Qual o prazo?">Três dias úteis</x-ui.accordion-item>
        </x-ui.accordion>
    BLADE);

    expect($rendered)->toContain('<details')
        ->and($rendered)->not->toContain('name=')
        ->and($rendered)->not->toContain('role="group"');
});

it('opens only the item marked as open', function (): void {
    expect(preg_match_all('/<details[^>]+\sopen/', (string) $this->blade(ACCORDION)))->toBe(1);
});

it('renders the icon of an item that asks for one, plus a chevron on every summary', function (): void {
    $rendered = (string) $this->blade(ACCORDION);

    expect(substr_count($rendered, 'group-open:rotate-180'))->toBe(3)
        ->and(substr_count($rendered, '<svg'))->toBe(4);
});

it('animates the height through the details-content pseudo element', function (): void {
    $rendered = html_entity_decode((string) $this->blade(ACCORDION));

    expect($rendered)->toContain('[&::details-content]:transition-[height,content-visibility]')
        ->and($rendered)->toContain('[&[open]::details-content]:h-auto');
});

it('merges attributes onto the group and onto the item content', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.accordion class="rounded-lg">
            <x-ui.accordion-item label="Qual o prazo?" class="prose">Três dias úteis</x-ui.accordion-item>
        </x-ui.accordion>
    BLADE);

    expect($rendered)->toContain('rounded-lg')
        ->and($rendered)->toContain('prose');
});

it('builds the items and the FAQPage schema from the faq shorthand', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.accordion name="duvidas" :faq="[
            'Qual o prazo?'   => 'Três dias úteis.',
            'Posso parcelar?' => 'Em até 12x.',
        ]" />
    BLADE);

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $rendered, $matches);

    expect(substr_count($rendered, '<details'))->toBe(2)
        ->and($rendered)->toContain('Qual o prazo?')
        ->and(substr_count($rendered, 'name="duvidas"'))->toBe(2)
        ->and(json_decode(trim($matches[1]), true))->toBe([
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => [
                [
                    '@type'          => 'Question',
                    'name'           => 'Qual o prazo?',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Três dias úteis.'],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'Posso parcelar?',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Em até 12x.'],
                ],
            ],
        ]);
});

it('emits no schema when the items come from the slot', function (): void {
    expect((string) $this->blade(ACCORDION))->not->toContain('application/ld+json');
});

it('eases the panel on the pseudo-element that animates', function (): void {
    /**
     * The height transition lives on `::details-content`, so every part of it needs that
     * variant. An unprefixed `ease-*` lands on the `<details>` box instead, which animates
     * nothing — the panel kept opening on the browser's default curve.
     */
    $rendered = (string) $this->blade('<x-ui.accordion-item label="Um">Dois</x-ui.accordion-item>');

    expect($rendered)
        ->toContain('[&::details-content]:ease-(--ease-fluid)')
        ->toContain('[&::details-content]:duration-(--duration-base)');
});
