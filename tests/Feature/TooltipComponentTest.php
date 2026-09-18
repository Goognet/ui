<?php

declare(strict_types = 1);

it('shows on hover and on focus alike', function (): void {
    $html = (string) $this->blade('<x-ui.tooltip text="Copiar link">Copiar</x-ui.tooltip>');

    expect($html)->toContain('group-hover:visible')
        ->toContain('group-focus-within:visible')
        ->toContain('role="tooltip"');
});

it('renders the trigger alone when there is nothing to say', function (): void {
    $html = (string) $this->blade('<x-ui.tooltip>Copiar</x-ui.tooltip>');

    expect(trim($html))->toBe('Copiar')
        ->and($html)->not->toContain('role="tooltip"');
});

it('hangs the bubble on the side it was told to', function (string $placement, string $class): void {
    expect((string) $this->blade('<x-ui.tooltip text="t" placement="' . $placement . '">x</x-ui.tooltip>'))
        ->toContain($class);
})->with([
    ['top', 'bottom-full'],
    ['bottom', 'top-full'],
    ['left', 'right-full'],
    ['right', 'left-full'],
]);

it('reaches the keyboard when the trigger cannot be focused', function (): void {
    $plain = (string) $this->blade('<x-ui.tooltip text="t">SLA</x-ui.tooltip>');

    expect($plain)->not->toContain('tabindex');

    $focusable = (string) $this->blade('<x-ui.tooltip text="t" focusable>SLA</x-ui.tooltip>');

    preg_match('/aria-describedby="([^"]+)"/', $focusable, $described);

    expect($focusable)->toContain('tabindex="0"')
        ->and($focusable)->toContain('id="' . $described[1] . '"');
});

it('gives two tooltips on the same page ids of their own', function (): void {
    $html = (string) $this->blade('<x-ui.tooltip text="a" focusable>A</x-ui.tooltip><x-ui.tooltip text="b" focusable>B</x-ui.tooltip>');

    preg_match_all('/aria-describedby="([^"]+)"/', $html, $matches);

    expect($matches[1])->toHaveCount(2)
        ->and($matches[1][0])->not->toBe($matches[1][1]);
});
