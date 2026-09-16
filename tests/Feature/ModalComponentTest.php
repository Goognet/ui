<?php

declare(strict_types = 1);

use Illuminate\View\ViewException;

const MODAL = <<<'BLADE'
<x-ui.modal name="orcamento" title="Peça um orçamento">
    <p>Conte o que você precisa.</p>

    <x-slot:footer>
        <x-ui.button data-modal-close>Cancelar</x-ui.button>
    </x-slot>
</x-ui.modal>
BLADE;

it('renders a dialog carrying the name as its id', function (): void {
    $this->blade(MODAL)
        ->assertSee('<dialog', false)
        ->assertSee('id="orcamento"', false)
        ->assertSee('data-modal', false);
});

it('ties the dialog to its own title', function (): void {
    $this->blade(MODAL)
        ->assertSee('aria-labelledby="orcamento-title"', false)
        ->assertSee('id="orcamento-title"', false)
        ->assertSee('Peça um orçamento');
});

it('renders the footer slot in its own region', function (): void {
    $rendered = (string) $this->blade(MODAL);

    expect($rendered)->toContain('Cancelar')
        ->and($rendered)->toContain('border-t');
});

it('offers a close button by default', function (): void {
    expect((string) $this->blade(MODAL))->toContain('data-modal-close')
        ->and((string) $this->blade(MODAL))->toContain('Fechar');
});

it('drops the close button and flags itself static when it cannot be closed', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.modal name="aviso" title="Confirme" :closable="false">Texto</x-ui.modal>
    BLADE);

    expect($rendered)->toContain('data-modal-static')
        ->and($rendered)->not->toContain('data-modal-close');
});

it('renders no header at all when it has neither title nor close button', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.modal name="aviso" :closable="false">Texto</x-ui.modal>
    BLADE);

    expect($rendered)->not->toContain('border-b')
        ->and($rendered)->not->toContain('aria-labelledby');
});

it('sizes the dialog through the size prop', function (): void {
    expect((string) $this->blade('<x-ui.modal name="a" size="xl">Texto</x-ui.modal>'))->toContain('max-w-4xl')
        ->and((string) $this->blade('<x-ui.modal name="a">Texto</x-ui.modal>'))->toContain('max-w-lg');
});

it('fades in from a starting frame so the dialog does not pop', function (): void {
    $rendered = html_entity_decode((string) $this->blade(MODAL));

    expect($rendered)->toContain('starting:open:opacity-0')
        ->and($rendered)->toContain('[transition-behavior:allow-discrete]')
        ->and($rendered)->toContain('backdrop:bg-neutral-950/50');
});

it('merges attributes onto the dialog', function (): void {
    expect((string) $this->blade('<x-ui.modal name="a" class="max-w-xs">Texto</x-ui.modal>'))->toContain('max-w-xs');
});

it('refuses to render without a name', function (): void {
    $this->blade('<x-ui.modal>Texto</x-ui.modal>');
})->throws(ViewException::class, 'requires a name');
