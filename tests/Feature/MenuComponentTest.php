<?php

declare(strict_types = 1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\ViewException;

/**
 * @return array<int, array<string, mixed>>
 */
function menuItems(): array
{
    return [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Serviços', 'children' => [
            ['label' => 'Consultoria', 'url' => '/consultoria'],
            ['label' => 'Auditoria', 'url' => '/auditoria'],
        ]],
        ['label' => 'Contato', 'url' => '/contato'],
    ];
}

function renderMenu(string $attributes = '', ?array $items = null): string
{
    return Blade::render(
        '<x-ui.menu :items="$items" ' . $attributes . ' />',
        ['items' => $items ?? menuItems()],
        deleteCachedView: true,
    );
}

it('renders a labelled nav marked for the script', function (): void {
    expect(renderMenu())->toContain('<nav aria-label="Menu principal"')
        ->toContain('data-menu');
});

it('takes a custom nav label', function (): void {
    expect(renderMenu('label="Menu do rodapé"'))->toContain('aria-label="Menu do rodapé"');
});

it('renders one desktop entry per item', function (): void {
    $rendered = renderMenu();

    expect(substr_count($rendered, 'Início'))->toBe(2)
        ->and(substr_count($rendered, 'Contato'))->toBe(2);
});

it('hides the desktop list below the large breakpoint', function (): void {
    expect(renderMenu())->toContain('class="hidden items-center gap-6 lg:flex"');
});

it('wires the hamburger to the drawer through aria-controls', function (): void {
    $rendered = renderMenu();

    preg_match('/data-menu-toggle\s+aria-controls="([^"]+)"/', $rendered, $toggle);
    preg_match('/id="([^"]+)"\s+class="[^"]*"\s+data-menu-panel/', $rendered, $panel);

    expect($toggle[1] ?? 'a')->toBe($panel[1] ?? 'b');
});

it('starts with the drawer closed', function (): void {
    $rendered = renderMenu();

    expect($rendered)->toContain('data-menu-toggle')
        ->toContain('aria-expanded="false"')
        ->toMatch('/data-menu-panel\s+data-state="closed"/')
        ->toMatch('/data-menu-overlay\s+data-state="closed"/');
});

it('marks the drawer as a modal dialog', function (): void {
    expect(renderMenu())->toContain('role="dialog"')
        ->toContain('aria-modal="true"');
});

it('names the hamburger and the close button for screen readers', function (): void {
    expect(renderMenu())->toContain('<span class="sr-only">Abrir menu</span>')
        ->toContain('<span class="sr-only">Fechar menu</span>')
        ->toContain('data-menu-close');
});

it('hides the hamburger and the drawer on large screens', function (): void {
    $rendered = renderMenu();

    expect(substr_count($rendered, 'lg:hidden'))->toBe(3);
});

it('renders a plain link for an item without children', function (): void {
    $rendered = renderMenu(items: [['label' => 'Contato', 'url' => '/contato']]);

    expect($rendered)->toContain('href="/contato"')
        ->and($rendered)->not->toContain('data-menu-dropdown');
});

it('renders a trigger and a panel for an item with children', function (): void {
    $rendered = renderMenu();

    preg_match_all('/data-menu-dropdown\s+data-state="closed"\s+aria-controls="([^"]+)"/', $rendered, $triggers);

    expect($triggers[1])->toHaveCount(2);

    foreach ($triggers[1] as $id) {
        expect($rendered)->toContain('id="' . $id . '"');
    }
});

it('renders the children in both the desktop dropdown and the mobile accordion', function (): void {
    $rendered = renderMenu();

    expect(substr_count($rendered, 'href="/consultoria"'))->toBe(2)
        ->and(substr_count($rendered, 'href="/auditoria"'))->toBe(2);
});

it('starts every submenu closed', function (): void {
    $rendered = renderMenu();

    expect(substr_count($rendered, 'data-menu-dropdown-panel'))->toBe(2)
        ->and(substr_count($rendered, 'aria-expanded="false"'))->toBe(3);
});

it('renders an icon on an item and on a child', function (): void {
    $rendered = renderMenu(items: [
        ['label' => 'Início', 'url' => '/', 'icon' => 'heroicon-m-home'],
        ['label' => 'Serviços', 'children' => [
            ['label' => 'Consultoria', 'url' => '/consultoria', 'icon' => 'heroicon-m-briefcase'],
        ]],
    ]);

    expect(substr_count($rendered, 'size-[1em]'))->toBe(4);
});

it('gives each menu on the page its own ids', function (): void {
    preg_match('/data-menu-panel/', renderMenu());

    preg_match('/aria-controls="([^"]+)"/', renderMenu(), $first);
    preg_match('/aria-controls="([^"]+)"/', renderMenu(), $second);

    expect($first[1])->not->toBe($second[1]);
});

it('merges attributes onto the nav', function (): void {
    expect(renderMenu('class="ms-auto"'))->toContain('ms-auto');
});

it('renders an empty menu without breaking', function (): void {
    $rendered = renderMenu(items: []);

    expect($rendered)->toContain('<nav')
        ->and($rendered)->not->toContain('data-menu-dropdown');
});

/**
 * @return array<int, array<string, mixed>>
 */
function menuWithMegamenu(): array
{
    return [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Serviços', 'groups' => [
            ['label' => 'Contábil', 'children' => [
                ['label' => 'Consultoria', 'url' => '/consultoria', 'description' => 'Sob medida'],
            ]],
            ['label' => 'Fiscal', 'children' => [
                ['label' => 'Apuração', 'url' => '/apuracao'],
            ]],
        ]],
        ['label' => 'Blog', 'children' => [
            ['label' => 'Artigos', 'url' => '/artigos'],
        ]],
    ];
}

it('renders a megamenu panel for an item carrying groups', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    expect($rendered)->toContain('lg:grid-cols-2')
        ->toContain('Contábil')
        ->toContain('Fiscal')
        ->toContain('Sob medida');
});

it('keeps groups and children as different desktop shapes', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    expect(substr_count($rendered, 'data-menu-dropdown-panel'))->toBe(4)
        ->and(substr_count($rendered, 'max-w-7xl'))->toBe(1)
        ->and(substr_count($rendered, 'min-w-64'))->toBe(1);
});

it('does not make the megamenu item a positioning context', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    expect(substr_count($rendered, '<li class="relative">'))->toBe(1);
});

it('flattens a megamenu into the drawer accordion with its group headings', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    expect(substr_count($rendered, 'href="/consultoria"'))->toBe(2)
        ->and(substr_count($rendered, 'href="/apuracao"'))->toBe(2)
        ->and(substr_count($rendered, 'Contábil'))->toBe(2);
});

it('gives the drawer one accordion per item that has a panel', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    preg_match_all('/aria-controls="([^"]*-accordion-\d+)"/', $rendered, $accordions);

    expect($accordions[1])->toHaveCount(2);
});

it('renders a drawer entry for every item, megamenu included', function (): void {
    $rendered = renderMenu(items: menuWithMegamenu());

    preg_match('/data-menu-panel.*$/s', $rendered, $drawer);

    expect($drawer[0])->toContain('Início')
        ->toContain('Serviços')
        ->toContain('Blog');
});

it('takes a per-item column count for the megamenu', function (): void {
    $items = [['label' => 'Serviços', 'columns' => 1, 'groups' => [
        ['label' => 'A', 'children' => [['label' => 'x', 'url' => '/x']]],
        ['label' => 'B', 'children' => [['label' => 'y', 'url' => '/y']]],
    ]]];

    expect(renderMenu(items: $items))->toContain('lg:grid-cols-1')
        ->and(renderMenu(items: $items))->not->toContain('lg:grid-cols-2');
});

it('states where the visitor is instead of only where they can go', function (): void {
    /** The rule under the label is the same one hover grows, so the bar speaks one language. */
    $rendered = renderMenu(items: [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Contato', 'url' => '/contato'],
    ]);

    expect($rendered)->toContain('aria-current="page"')
        ->and(substr_count($rendered, 'after:scale-x-100'))->toBeGreaterThanOrEqual(1);
});

it('marks a parent whose child is the current page', function (): void {
    $rendered = renderMenu(items: [
        ['label' => 'Serviços', 'children' => [
            ['label' => 'Consultoria', 'url' => '/'],
        ]],
    ]);

    /** A dropdown has no url of its own; it is current when one of its children is. */
    expect($rendered)->toContain('after:scale-x-100');
});

it('colours the resting item rather than fading it', function (): void {
    /** `opacity-70` dimmed the icon with the label and dropped the text below the contrast
     *  the rest of the page holds. */
    $rendered = renderMenu(items: [['label' => 'Início', 'url' => '/x']]);

    expect($rendered)->toContain('text-neutral-600')
        ->toContain('hover:text-neutral-900')
        ->not->toContain('opacity-70');
});

it('gives a dropdown child room for a description', function (): void {
    $rendered = renderMenu(items: [
        ['label' => 'Blog', 'children' => [
            ['label' => 'Artigos', 'url' => '/artigos', 'description' => 'Publicados toda semana'],
        ]],
    ]);

    expect($rendered)->toContain('Publicados toda semana');
});

it('keeps the section lit on the pages beneath it', function (string $path, string $expected): void {
    /**
     * Exact matching alone left every article page with the whole bar unlit — a silence that
     * arrives the moment the site grows a section with pages under it.
     */
    $this->app->instance('request', Request::create(url($path)));

    $items = [
        ['label' => 'Início', 'url' => '/'],
        ['label' => 'Blog', 'url' => '/blog'],
        ['label' => 'Contato', 'url' => '/contato'],
    ];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    preg_match_all('/aria-current="page"[^>]*>\s*([^<\n]+)/', $rendered, $matches);

    $lit = collect($matches[1])->map(fn (string $label): string => trim($label))->unique()->values()->all();

    expect($lit)->toBe($expected === '' ? [] : [$expected]);
})->with([
    'a própria seção'    => ['/blog', 'Blog'],
    'um artigo dentro'   => ['/blog/meu-artigo', 'Blog'],
    'dois níveis abaixo' => ['/blog/categoria/seo', 'Blog'],
    'outra seção'        => ['/contato', 'Contato'],
    /** The trailing slash is what stops `/blog` from claiming a different word that starts the same. */
    'nome parecido' => ['/blog-antigo', ''],
]);

it('never lets the home item claim the whole site', function (): void {
    /** Every address on the site starts with the root, so as a prefix it would light up everywhere. */
    $this->app->instance('request', Request::create(url('/contato')));

    $items = [['label' => 'Início', 'url' => '/'], ['label' => 'Contato', 'url' => '/contato']];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    preg_match_all('/aria-current="page"[^>]*>\s*([^<\n]+)/', $rendered, $matches);

    expect(collect($matches[1])->map(fn (string $l): string => trim($l))->unique()->values()->all())->toBe(['Contato']);
});

it('lets the item settle its own state', function (): void {
    /** For a landing page that belongs to a section it does not sit below, and the reverse. */
    $this->app->instance('request', Request::create(url('/contato')));

    $items = [
        ['label' => 'Blog', 'url' => '/blog', 'current' => true],
        ['label' => 'Contato', 'url' => '/contato', 'current' => false],
    ];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    preg_match_all('/aria-current="page"[^>]*>\s*([^<\n]+)/', $rendered, $matches);

    expect(collect($matches[1])->map(fn (string $l): string => trim($l))->unique()->values()->all())->toBe(['Blog']);
});

it('carries a badge as a string or with a colour', function (): void {
    $items = [
        ['label' => 'Blog', 'url' => '/blog', 'badge' => 'Novo'],
        ['label' => 'Vagas', 'url' => '/vagas', 'badge' => ['label' => '2', 'variant' => 'primary']],
    ];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    expect($rendered)->toContain('Novo')->toContain('bg-primary');
});

it('badges a dropdown trigger too, not only a plain link', function (): void {
    $items = [['label' => 'Blog', 'badge' => 'Novo', 'children' => [['label' => 'Artigos', 'url' => '/artigos']]]];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    expect($rendered)->toMatch('/<button[^>]*>.*?Novo.*?<\/button>/s');
});

it('says where you are in the drawer as well as in the bar', function (): void {
    /** The drawer marked nothing before: on a phone the menu never said which page you were on. */
    $this->app->instance('request', Request::create(url('/contato')));

    $items = [['label' => 'Início', 'url' => '/'], ['label' => 'Contato', 'url' => '/contato']];

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => $items]);

    /** Twice: once in the bar, once in the drawer — both halves render from the same list. */
    expect(substr_count($rendered, 'aria-current="page"'))->toBe(2);
});

it('takes a route name as well as a path', function (): void {
    /**
     * A literal path in `config/global.php` has to be remembered twice: change the route and
     * the menu keeps pointing at the old address, with nothing on screen to say so. The name
     * survives the change. It cannot be resolved in the config file itself — config is read
     * during bootstrap, before the router exists.
     */
    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Início', 'route' => 'home'],
        ['label' => 'Privacidade', 'route' => 'privacy'],
    ]]);

    expect($rendered)
        ->toContain('href="' . route('home') . '"')
        ->toContain('href="' . route('privacy') . '"');
});

it('passes parameters through to the route', function (): void {
    Route::view('/artigos/{slug}', 'pages.home')->name('posts.show');

    /**
     * The name lookup table is built once while the app boots, so a route registered inside a
     * test is invisible to `Route::has()` until it is rebuilt. Measured: false before, true
     * after. It is an artefact of registering at run time — in an application the routes file
     * runs before anything asks.
     */
    Route::getRoutes()->refreshNameLookups();

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Artigo', 'route' => ['posts.show', ['slug' => 'meu-post']]],
    ]]);

    expect($rendered)->toContain('href="' . route('posts.show', ['slug' => 'meu-post']) . '"');
});

it('prefers the route name when both are given', function (): void {
    /** The name is the more specific of the two, and the one that survives a path change. */
    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Início', 'route' => 'home', 'url' => '/caminho-velho'],
    ]]);

    expect($rendered)->toContain(route('home'))->not->toContain('/caminho-velho');
});

it('resolves a route name inside a dropdown and a megamenu too', function (): void {
    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Mais', 'children' => [['label' => 'Privacidade', 'route' => 'privacy']]],
        ['label' => 'Serviços', 'groups' => [
            ['label' => 'Grupo', 'children' => [['label' => 'Início', 'route' => 'home']]],
        ]],
    ]]);

    expect($rendered)
        ->toContain('href="' . route('privacy') . '"')
        ->toContain('href="' . route('home') . '"');
});

it('names the missing route and the item that asked for it', function (): void {
    /** Laravel's own message names the route; this one also names where the typo lives. */
    expect(fn () => $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Blog', 'route' => 'blog.index'],
    ]]))->toThrow(ViewException::class, 'rota "blog.index", que não existe. Item: "Blog"');
});

it('sends a shared anchor to the home page from another page', function (): void {
    /**
     * The same list renders on every page. On `/politica-de-privacidade` a bare `#servicos`
     * pointed at a section that is not there — and nothing on screen said so.
     */
    $this->app->instance('request', Request::create(url('/politica-de-privacidade')));

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Serviços', 'url' => '#servicos'],
    ]]);

    expect($rendered)->toContain(rtrim(url('/'), '/') . '/#servicos');
});

it('keeps the anchor bare on the home page, so the scroll still eases', function (): void {
    $this->app->instance('request', Request::create(url('/')));

    $rendered = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Serviços', 'url' => '#servicos'],
    ]]);

    expect($rendered)->toContain('href="#servicos"');
});
