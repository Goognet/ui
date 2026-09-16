<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Whatsapp;

beforeEach(function (): void {
    config()->set('goognet-ui.whatsapp.number', '');
    config()->set('goognet-ui.whatsapp.message', '');
});

it('builds the url from the explicit props', function (): void {
    $this->blade('<x-ui.whatsapp phone="11999999999" message="Oi!">Falar</x-ui.whatsapp>')
        ->assertSee('https://wa.me/5511999999999?text=Oi%21', false);
});

it('falls back to the global config when no props are given', function (): void {
    config()->set('goognet-ui.whatsapp.number', '11988887777');
    config()->set('goognet-ui.whatsapp.message', 'Vim pelo site');

    $this->blade('<x-ui.whatsapp>Falar</x-ui.whatsapp>')
        ->assertSee('https://wa.me/5511988887777?text=Vim%20pelo%20site', false);
});

it('prefers the props over the global config', function (): void {
    config()->set('goognet-ui.whatsapp.number', '11988887777');
    config()->set('goognet-ui.whatsapp.message', 'Vim pelo site');

    $this->blade('<x-ui.whatsapp phone="11912345678" message="Outra">Falar</x-ui.whatsapp>')
        ->assertSee('https://wa.me/5511912345678?text=Outra', false);
});

it('treats an empty config value as missing instead of using it', function (): void {
    config()->set('goognet-ui.whatsapp.message', '');

    $this->blade('<x-ui.whatsapp phone="11999999999">Falar</x-ui.whatsapp>')
        ->assertSee(rawurlencode('Olá! Vim pelo site e gostaria de mais informações.'), false);
});

it('strips the country code when the number already carries it', function (string $phone): void {
    $this->blade('<x-ui.whatsapp phone="' . $phone . '" message="Oi">Falar</x-ui.whatsapp>')
        ->assertSee('https://wa.me/5511999999999?text=Oi', false);
})->with([
    '11999999999',
    '5511999999999',
    '(11) 99999-9999',
    '+55 (11) 99999-9999',
]);

it('renders an external button carrying the slot', function (): void {
    $this->blade('<x-ui.whatsapp phone="11999999999">Fale conosco</x-ui.whatsapp>')
        ->assertSee('Fale conosco')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

it('merges attributes onto the button', function (): void {
    $this->blade('<x-ui.whatsapp phone="11999999999" class="w-full">Falar</x-ui.whatsapp>')
        ->assertSee('w-full', false);
});

it('is an anchor, not an anchor wrapped in a block', function (): void {
    /**
     * The wrapper `<div>` made every WhatsApp link block-level: it could not sit in a row of
     * buttons, and each call site fought it back with classes. Three of them were copying the
     * button's whole class string by hand because of it.
     */
    $rendered = trim((string) $this->blade('<x-ui.whatsapp>Falar</x-ui.whatsapp>'));

    expect($rendered)->toStartWith('<a ')
        ->and($rendered)->toEndWith('</a>');
});

it('hands its url to any component that takes an href', function (): void {
    /** `Goognet\Ui\Support\Whatsapp::url()` is what let the header and the footer use x-ui.button directly. */
    expect(Whatsapp::url())->toStartWith('https://wa.me/')
        ->and(Whatsapp::url(phone: '5511999999999'))->toContain('5511999999999');
});
