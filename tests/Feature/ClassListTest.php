<?php

declare(strict_types = 1);

use Goognet\Ui\Support\ClassList;

it('recognises a text colour', function (string $classes): void {
    expect(ClassList::setsColor($classes, 'text'))->toBeTrue();
})->with([
    'palette'       => 'text-blue-700',
    'theme colour'  => 'text-primary',
    'derived token' => 'text-primary-ink',
    'white'         => 'text-white',
    'current'       => 'text-current',
    'with opacity'  => 'text-neutral-900/80',
    'arbitrary hex' => 'text-[#ff0000]',
    'css variable'  => 'text-(--brand)',
    'important'     => '!text-red-700',
    'among others'  => 'mt-4 font-bold text-amber-600 underline',
]);

it('does not mistake other text utilities for a colour', function (string $classes): void {
    expect(ClassList::setsColor($classes, 'text'))->toBeFalse();
})->with([
    'size'             => 'text-lg',
    'large size'       => 'text-4xl',
    'alignment'        => 'text-center',
    'wrapping'         => 'text-balance',
    'arbitrary length' => 'text-[14px]',
    'typed length'     => 'text-(length:--size)',
    'hover only'       => 'hover:text-red-700',
    'breakpoint only'  => 'md:text-red-700',
    'nothing'          => '',
]);

it('tells background colours from background sizing and images', function (): void {
    expect(ClassList::setsColor('bg-red-100', 'bg'))->toBeTrue()
        ->and(ClassList::setsColor('bg-cover bg-center bg-no-repeat', 'bg'))->toBeFalse()
        ->and(ClassList::setsColor('bg-linear-to-r', 'bg'))->toBeFalse()
        ->and(ClassList::setsColor('bg-[url(/a.jpg)]', 'bg'))->toBeFalse();
});

it('tells border colours from border widths and styles', function (): void {
    expect(ClassList::setsColor('border-red-300', 'border'))->toBeTrue()
        ->and(ClassList::setsColor('border-2 border-dashed border-t', 'border'))->toBeFalse();
});

it('drops the default only when the caller set a colour', function (): void {
    expect(ClassList::colorUnlessSet('text-blue-700', 'text', 'text-neutral-700'))->toBeEmpty()
        ->and(ClassList::colorUnlessSet('text-lg', 'text', 'text-neutral-700'))->toBe('text-neutral-700');
});

it('checks a variant colour apart from the resting one', function (): void {
    expect(ClassList::setsColor('hover:text-blue-700', 'text', 'hover'))->toBeTrue()
        ->and(ClassList::setsColor('text-blue-700', 'text', 'hover'))->toBeFalse()
        ->and(ClassList::setsColor('hover:text-lg', 'text', 'hover'))->toBeFalse()
        ->and(ClassList::setsColor('md:hover:text-blue-700', 'text', 'hover'))->toBeFalse();
});
