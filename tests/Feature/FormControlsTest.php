<?php

declare(strict_types = 1);

use Goognet\Ui\Support\FormControl;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

function withErrors(array $messages): void
{
    $bag = new ViewErrorBag();

    $bag->put('default', new MessageBag($messages));

    view()->share('errors', $bag);
}

it('ties the label, the hint and the error to the control', function (): void {
    $html = (string) $this->blade('<x-ui.input name="email" label="E-mail" hint="Só para responder" />');

    expect($html)->toContain('<label for="email"')
        ->toContain('id="email-hint"')
        ->toContain('aria-describedby="email-hint"');
});

it('reads the message the validator left for the field', function (): void {
    withErrors(['email' => 'Informe um e-mail válido']);

    $html = (string) $this->blade('<x-ui.input name="email" label="E-mail" />');

    expect($html)->toContain('Informe um e-mail válido')
        ->toContain('aria-invalid="true"')
        ->toContain('aria-describedby="email-error"')
        ->toContain('role="alert"')
        ->toContain(FormControl::INVALID);

    view()->share('errors', new ViewErrorBag());
});

it('finds the message of a field whose name is an array', function (): void {
    withErrors(['items.0.qty' => 'Quantidade inválida']);

    expect((string) $this->blade('<x-ui.input name="items[0][qty]" />'))
        ->toContain('Quantidade inválida')
        ->toContain('id="items-0-qty"');

    view()->share('errors', new ViewErrorBag());
});

it('lets a message passed at the call site win over the bag', function (): void {
    withErrors(['email' => 'Do validador']);

    expect((string) $this->blade('<x-ui.input name="email" error="Da chamada" />'))
        ->toContain('Da chamada')
        ->not->toContain('Do validador');

    view()->share('errors', new ViewErrorBag());
});

it('refuses a type that would turn the field into something else', function (string $type): void {
    expect((string) $this->blade('<x-ui.input name="x" type="' . $type . '" />'))
        ->toContain('type="text"')
        ->not->toContain('type="' . $type . '"');
})->with(['file', 'submit', 'image', 'button', 'checkbox']);

it('keeps the types a text field is allowed to have', function (string $type): void {
    expect((string) $this->blade('<x-ui.input name="x" type="' . $type . '" />'))->toContain('type="' . $type . '"');
})->with(FormControl::TYPES);

it('repopulates a field from the last submission, except a password', function (): void {
    session()->flashInput(['email' => 'thiago@goognet.com.br', 'senha' => 'secreta']);

    $html = (string) $this->blade('<x-ui.input name="email" /><x-ui.input name="senha" type="password" />');

    expect($html)->toContain('value="thiago@goognet.com.br"')
        ->not->toContain('secreta');
});

it('dresses the wrapper with the call site class and the control with control-class', function (): void {
    $html = (string) $this->blade('<x-ui.input name="x" class="mt-6" control-class="font-mono" />');

    expect($html)->toContain('flex flex-col gap-1.5 mt-6')
        ->toContain('font-mono');
});

it('takes options as a map, as a list and as rows', function (mixed $options): void {
    $html = (string) $this->blade('<x-ui.select name="uf" :options="$options" selected="sp" />', ['options' => $options]);

    expect($html)->toContain('<option value="sp" selected>')
        ->toContain('São Paulo');
})->with([
    'map'  => fn (): array => ['sp' => 'São Paulo', 'rj' => 'Rio'],
    'rows' => fn (): array => [['value' => 'sp', 'label' => 'São Paulo'], ['value' => 'rj', 'label' => 'Rio']],
    'ids'  => fn (): array => [['id' => 'sp', 'name' => 'São Paulo'], ['id' => 'rj', 'name' => 'Rio']],
]);

it('puts a placeholder option first and selects it while nothing is chosen', function (): void {
    expect((string) $this->blade('<x-ui.select name="uf" placeholder="Escolha" :options="[\'sp\' => \'SP\']" />'))
        ->toContain('<option value="" selected>Escolha</option>');
});

it('renders the textarea content from the slot and from the last submission', function (): void {
    expect((string) $this->blade('<x-ui.textarea name="msg">Do slot</x-ui.textarea>'))->toContain('>Do slot</textarea>');

    session()->flashInput(['msg' => 'Do envio anterior']);

    expect((string) $this->blade('<x-ui.textarea name="msg" />'))->toContain('>Do envio anterior</textarea>');
});

it('marks a required field for the browser and for the reader', function (): void {
    expect((string) $this->blade('<x-ui.input name="x" label="Nome" required />'))
        ->toContain('required')
        ->toContain('aria-hidden="true">*</span>');
});

it('escapes what the validator and the options carry', function (): void {
    withErrors(['x' => '<script>alert(1)</script>']);

    expect((string) $this->blade('<x-ui.select name="x" :options="[\'<img src=x>\' => \'<b>rótulo</b>\']" />'))
        ->not->toContain('<script>')
        ->not->toContain('<b>rótulo</b>')
        ->not->toContain('<img src=x>');

    view()->share('errors', new ViewErrorBag());
});
