<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Blade;

/**
 * @return array<int, array<string, mixed>>
 */
function megamenuGroups(int $count = 2): array
{
    return collect(range(1, $count))
        ->map(fn (int $i): array => [
            'label'    => 'Grupo ' . $i,
            'children' => [
                [
                    'label'       => 'Item ' . $i . 'a',
                    'url'         => '/item-' . $i . 'a',
                    'icon'        => 'heroicon-m-briefcase',
                    'description' => 'Descrição ' . $i . 'a',
                ],
                ['label' => 'Item ' . $i . 'b', 'url' => '/item-' . $i . 'b'],
            ],
        ])
        ->all();
}

function renderMegamenu(string $attributes = '', ?array $groups = null, string $slot = ''): string
{
    $tag = $slot === ''
        ? '<x-ui.megamenu label="Serviços" :groups="$groups" ' . $attributes . ' />'
        : '<x-ui.megamenu label="Serviços" :groups="$groups" ' . $attributes . '>' . $slot . '</x-ui.megamenu>';

    return Blade::render(
        $tag,
        ['groups' => $groups ?? megamenuGroups()],
        deleteCachedView: true,
    );
}

it('renders a trigger carrying the label', function (): void {
    expect(renderMegamenu())->toContain('Serviços')
        ->toContain('type="button"')
        ->toContain('data-menu-dropdown');
});

it('reuses the menu script hooks so no second behaviour is needed', function (): void {
    expect(renderMegamenu())->toContain('data-menu')
        ->toContain('data-menu-dropdown')
        ->toContain('data-menu-dropdown-panel');
});

it('wires the trigger to the panel through aria-controls', function (): void {
    $rendered = renderMegamenu();

    preg_match('/aria-controls="([^"]+)"/', $rendered, $trigger);

    expect($rendered)->toContain('id="' . $trigger[1] . '"');
});

it('starts closed', function (): void {
    expect(renderMegamenu())->toContain('aria-expanded="false"')
        ->toMatch('/data-menu-dropdown-panel\s+data-state="closed"/');
});

it('gives each megamenu on the page its own ids', function (): void {
    preg_match('/aria-controls="([^"]+)"/', renderMegamenu(), $first);
    preg_match('/aria-controls="([^"]+)"/', renderMegamenu(), $second);

    expect($first[1])->not->toBe($second[1]);
});

it('lays the groups out in one column per group', function (int $count, string $expected): void {
    expect(renderMegamenu(groups: megamenuGroups($count)))->toContain($expected);
})->with([
    [1, 'lg:grid-cols-1'],
    [2, 'lg:grid-cols-2'],
    [3, 'lg:grid-cols-3'],
    [4, 'lg:grid-cols-4'],
]);

it('caps the column count at four', function (): void {
    expect(renderMegamenu(groups: megamenuGroups(6)))->toContain('lg:grid-cols-4');
});

it('takes an explicit column count', function (): void {
    expect(renderMegamenu('columns="2"', megamenuGroups(4)))->toContain('lg:grid-cols-2')
        ->and(renderMegamenu('columns="2"', megamenuGroups(4)))->not->toContain('lg:grid-cols-4');
});

it('renders a group heading', function (): void {
    expect(renderMegamenu())->toContain('Grupo 1')
        ->toContain('Grupo 2');
});

it('omits the heading when a group has no label', function (): void {
    $groups = [['children' => [['label' => 'Solto', 'url' => '/solto']]]];

    expect(renderMegamenu(groups: $groups))->not->toContain('uppercase');
});

it('renders every child as a link', function (): void {
    $rendered = renderMegamenu();

    expect($rendered)->toContain('href="/item-1a"')
        ->toContain('href="/item-1b"')
        ->toContain('href="/item-2a"');
});

it('renders the icon, the label and the description of a child', function (): void {
    $rendered = renderMegamenu();

    expect($rendered)->toContain('<span class="font-medium text-neutral-900">Item 1a</span>')
        ->toContain('<span class="text-sm text-neutral-500">Descrição 1a</span>')
        ->and(substr_count($rendered, 'size-9 shrink-0'))->toBe(2);
});

it('leaves out the icon box and the description when a child has neither', function (): void {
    $groups   = [['label' => 'G', 'children' => [['label' => 'Simples', 'url' => '/simples']]]];
    $rendered = renderMegamenu(groups: $groups);

    expect($rendered)->toContain('Simples')
        ->and($rendered)->not->toContain('size-9 shrink-0')
        ->and($rendered)->not->toContain('text-sm text-neutral-500');
});

it('stacks on mobile and floats over the page from the large breakpoint', function (): void {
    $rendered = renderMegamenu();

    expect($rendered)->toContain('hidden data-[state=open]:block')
        ->toContain('lg:absolute')
        ->toContain('lg:max-w-page');
});

it('renders a footer slot only when given one', function (): void {
    expect(renderMegamenu(slot: '<span>Ver todos</span>'))->toContain('Ver todos')
        ->toContain('border-t')
        ->and(renderMegamenu())->not->toContain('border-t');
});

it('merges attributes onto the root', function (): void {
    expect(renderMegamenu('class="ms-4"'))->toContain('ms-4');
});

it('renders without groups rather than breaking', function (): void {
    expect(renderMegamenu(groups: []))->toContain('Serviços')
        ->toContain('data-menu-dropdown');
});
