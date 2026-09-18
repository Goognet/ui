<?php

declare(strict_types = 1);

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ViewException;

it('ties each choice to its own id, so two of them coexist', function (): void {
    $html = (string) $this->blade('<x-ui.radio name="plano" value="pro" label="Pro" /><x-ui.radio name="plano" value="lite" label="Lite" />');

    expect($html)->toContain('id="plano-pro"')
        ->toContain('id="plano-lite"')
        ->toContain('for="plano-pro"')
        ->toContain('for="plano-lite"');
});

it('checks the option the last submission carried', function (): void {
    session()->flashInput(['plano' => 'lite']);

    $pro  = (string) $this->blade('<x-ui.radio name="plano" value="pro" label="Pro" />');
    $lite = (string) $this->blade('<x-ui.radio name="plano" value="lite" label="Lite" />');

    expect($pro)->not->toContain('checked')
        ->and($lite)->toContain('checked');
});

it('reads the validator message and points the control at it', function (): void {
    $bag = new ViewErrorBag();

    $bag->put('default', new MessageBag(['aceite' => 'É preciso aceitar os termos']));

    view()->share('errors', $bag);

    expect((string) $this->blade('<x-ui.checkbox name="aceite" label="Aceito" />'))
        ->toContain('É preciso aceitar os termos')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="aceite-1-error"')
        ->toContain('role="alert"');

    view()->share('errors', new ViewErrorBag());
});

it('asks a radio for the value it sends', function (): void {
    expect(fn () => $this->blade('<x-ui.radio name="plano" label="Pro" />'))->toThrow(ViewException::class);
});

it('rounds the checkbox and the radio differently, from one shared look', function (): void {
    expect((string) $this->blade('<x-ui.checkbox name="a" />'))->toContain('accent-primary')->toContain('rounded-sm')
        ->and((string) $this->blade('<x-ui.radio name="a" value="1" />'))->toContain('accent-primary')
        ->toContain('rounded-full');
});
