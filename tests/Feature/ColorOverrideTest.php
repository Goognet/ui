<?php

declare(strict_types = 1);

/**
 * `text-blue-700` sorts before `text-neutral-*` in the stylesheet, so if both reach the element
 * the component's grey wins and the colour written by hand never shows. Blue is chosen because
 * red sorts after neutral and would pass by accident.
 *
 * @return list<string>
 */
function textColors(string $html): array
{
    preg_match('/class="([^"]*)"/', $html, $matches);

    return array_values(array_filter(
        explode(' ', $matches[1] ?? ''),
        fn (string $class): bool => preg_match('/^text-(neutral|blue|current|primary|white)/', $class) === 1,
    ));
}

it('keeps only the colour written by hand', function (string $template): void {
    expect(textColors((string) $this->blade($template)))->toBe(['text-blue-700']);
})->with([
    'heading' => '<x-ui.heading class="text-blue-700">T</x-ui.heading>',
    'text'    => '<x-ui.text class="text-blue-700">T</x-ui.text>',
    'strong'  => '<x-ui.text variant="strong" class="text-blue-700">T</x-ui.text>',
    'link'    => '<x-ui.link href="/" class="text-blue-700">T</x-ui.link>',
    'badge'   => '<x-ui.badge class="text-blue-700">T</x-ui.badge>',
]);

it('keeps the default colour when the class only changes the size', function (string $template, string $default): void {
    expect(textColors((string) $this->blade($template)))->toBe([$default]);
})->with([
    'heading' => ['<x-ui.heading class="text-4xl">T</x-ui.heading>', 'text-neutral-950'],
    'text'    => ['<x-ui.text class="text-lg">T</x-ui.text>', 'text-neutral-700'],
    'badge'   => ['<x-ui.badge class="text-lg">T</x-ui.badge>', 'text-neutral-800'],
]);

it('replaces the badge fill and border by hand, hover included', function (): void {
    $html = (string) $this->blade('<x-ui.badge href="/" class="bg-blue-100 border-blue-300">T</x-ui.badge>');

    expect($html)->toContain('bg-blue-100')
        ->toContain('border-blue-300')
        ->not->toContain('bg-white')
        ->not->toContain('border-neutral-200')
        ->not->toContain('hover:bg-neutral-50');
});

it('replaces the link hover colour by hand', function (): void {
    $html = (string) $this->blade('<x-ui.link href="/" class="hover:text-blue-700">T</x-ui.link>');

    expect($html)->toContain('hover:text-blue-700')
        ->not->toContain('hover:text-neutral-900');
});
