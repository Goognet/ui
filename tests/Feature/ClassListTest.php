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

it('lets a caller utility replace the component utility for the same property', function (string $component, string $caller, string $expected): void {
    expect(ClassList::merge($component, $caller))->toBe($expected);
})->with([
    'radius'           => ['inline-flex rounded-control', 'rounded-full', 'inline-flex rounded-full'],
    'height'           => ['h-control px-4', 'h-14', 'px-4 h-14'],
    'size clears h, w' => ['h-10 w-10 text-sm', 'size-12', 'text-sm size-12'],
    'padding axis'     => ['px-4 py-2', 'px-8', 'py-2 px-8'],
    'padding all'      => ['px-4 py-2 pt-1', 'p-0', 'p-0'],
    'font weight'      => ['font-control text-sm', 'font-bold', 'text-sm font-bold'],
    'text size'        => ['text-sm text-neutral-800', 'text-lg', 'text-neutral-800 text-lg'],
    'text colour'      => ['text-sm text-neutral-800', 'text-blue-700', 'text-sm text-blue-700'],
    'background'       => ['bg-white shadow-soft', 'bg-black', 'shadow-soft bg-black'],
    'border colour'    => ['border border-neutral-200', 'border-red-500', 'border border-red-500'],
    'border width'     => ['border border-neutral-200', 'border-2', 'border-neutral-200 border-2'],
    'shadow'           => ['shadow-soft hover:shadow-lifted', 'shadow-none', 'hover:shadow-lifted shadow-none'],
    'transform'        => ['font-medium', 'uppercase', 'font-medium uppercase'],
    'display'          => ['inline-flex items-center', 'flex', 'items-center flex'],
    'arbitrary radius' => ['rounded-lg', 'rounded-[3px]', 'rounded-[3px]'],
    'important'        => ['rounded-lg', '!rounded-none', '!rounded-none'],
]);

it('matches conflicts per variant', function (): void {
    expect(ClassList::merge('bg-white hover:bg-neutral-50', 'hover:bg-red-100'))->toBe('bg-white hover:bg-red-100')
        ->and(ClassList::merge('bg-white hover:bg-neutral-50', 'bg-red-100'))->toBe('hover:bg-neutral-50 bg-red-100')
        ->and(ClassList::merge('data-[state=open]:rotate-180 rounded-lg', 'data-[state=open]:rounded-none'))->toBe('data-[state=open]:rotate-180 rounded-lg data-[state=open]:rounded-none')
        ->and(ClassList::merge('md:px-4', 'px-2'))->toBe('md:px-4 px-2');
});

it('never removes a class it does not recognise', function (): void {
    expect(ClassList::merge('group relative isolate hover:-translate-y-px text-pretty custom-thing', 'uppercase'))
        ->toBe('group relative isolate hover:-translate-y-px text-pretty custom-thing uppercase');
});

it('does not mistake a size for a colour or a side border for a width', function (): void {
    expect(ClassList::merge('text-neutral-800', 'text-lg'))->toBe('text-neutral-800 text-lg')
        ->and(ClassList::merge('border border-neutral-200', 'border-t'))->toBe('border border-neutral-200 border-t')
        ->and(ClassList::merge('text-sm', 'text-balance'))->toBe('text-sm text-balance');
});

it('stacks more than two layers, the last one winning', function (): void {
    expect(ClassList::merge('rounded-control h-control', 'rounded-none', 'rounded-full h-14'))->toBe('rounded-full h-14');
});

it('keeps each class once', function (): void {
    expect(ClassList::merge('inline-flex font-medium', 'inline-flex'))->toBe('font-medium inline-flex');
});
