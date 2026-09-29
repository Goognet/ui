@props([
    /** The page's own title, appended to the catalogue's name. */
    'heading' => null,

    /**
     * Resolves a link: `null` is the index, a name is that component's page. Passed in rather
     * than built here, because the published site and the page served inside a site address
     * each other differently — relative folders on GitHub Pages, named routes in a site.
     */
    'link',

    /** The component this page is about, so the rail can mark it. */
    'current' => null,
])

@php
    use Goognet\Ui\Support\Catalogue;

    $isPublic = (bool) config('goognet-ui.catalogue.public');

    $version = (string) config('goognet-ui.catalogue.version');

    $groups = Catalogue::groups();

    $titleOf = collect(Catalogue::entries())->pluck('title', 'name');
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @if ($isPublic)
        <meta
            name="description"
            content="{{ $heading === null ? 'Componentes Blade para Laravel e Tailwind CSS 4, acessíveis e com filtro de URL em todo href e src.' : $heading . ' — componente Blade do goognet/ui para Laravel e Tailwind CSS 4.' }}"
        />
        <title>
            {{ $heading === null ? 'goognet/ui · Componentes Blade para Laravel' : $heading . ' · goognet/ui' }}
        </title>
        <link
            rel="icon"
            href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23bef264'/%3E%3Cpath d='M9 16h14M16 9v14' stroke='%23191c17' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E"
        />
    @else
        <meta name="robots" content="noindex, nofollow" />
        <title>{{ $heading === null ? 'Componentes' : $heading }} · {{ config('app.name') }}</title>
    @endif

    @vite((array) config('goognet-ui.catalogue.vite'))

    <style>
        :root {
            --doc-bg: #f3f4f1;
            --doc-surface: #ffffff;
            --doc-sunken: #fbfbf9;
            --doc-ink: #191c17;
            --doc-ink-2: #3a3f35;
            --doc-muted: #58624e;
            /* 4.6:1 on the page ground, where #a7b09d measured 2.0:1 — this tone labels the prop tables. */
            --doc-faint: #67715d;
            --doc-line: #e4e7e0;
            --doc-line-2: #ccd1c5;
            --doc-accent: #bef264;
        }
    </style>
</head>
<body class="bg-[var(--doc-bg)] text-[var(--doc-ink)]">
    <header class="sticky top-0 z-20 border-b border-[var(--doc-line)] bg-[var(--doc-surface)]/85 backdrop-blur">
        <div class="mx-auto flex h-14 max-w-7xl items-center gap-4 px-6 lg:px-8">
            {{-- `min-w-0`: without it the link refuses to shrink and the truncate below never fires. --}}
            <a href="{{ $link(null) }}" class="inline-flex min-w-0 items-center gap-4">
                {{-- The package's own mark, inline: it ships no asset the site's Vite could serve. The file is part of the package. --}}
                <span
                    class="[&>svg]:h-full [&>svg]:w-auto inline-flex h-6 shrink-0"
                    role="img"
                    aria-label="Goognet"
                >{!! Catalogue::logo() !!}</span>

                <span class="h-5 w-px shrink-0 bg-[var(--doc-line-2)]"></span>

                <span class="truncate text-sm font-medium text-[var(--doc-muted)]">Biblioteca de componentes</span>
            </a>

            @if ($isPublic)
                @if (filled($version))
                    <span class="ms-auto rounded-full bg-[var(--doc-accent)] px-2.5 py-1 font-mono text-[11px] font-semibold text-[var(--doc-ink)]">{{ $version }}</span>
                @endif

                <a
                    href="https://github.com/Goognet/ui"
                    @class(['text-sm font-medium text-[var(--doc-muted)] hover:text-[var(--doc-ink)]', 'ms-auto' => blank($version)])
                >GitHub</a>
            @else
                <span class="ms-auto rounded-full bg-[var(--doc-accent)] px-2.5 py-1 text-[11px] font-semibold tracking-wide text-[var(--doc-ink)] uppercase">
                    apenas em dev
                </span>
            @endif
        </div>
    </header>

    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-10 px-6 lg:grid-cols-[14rem_minmax(0,1fr)] lg:px-8">
        <aside class="sticky top-14 hidden max-h-[calc(100svh-3.5rem)] flex-col self-start py-10 lg:flex">
            <nav
                aria-label="Componentes"
                class="flex min-h-0 [scrollbar-gutter:stable] flex-col gap-5 overflow-y-auto overscroll-contain"
            >
                <a
                    href="{{ $link(null) }}"
                    @class([
                        'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)]',
                        'bg-[var(--doc-surface)] text-[var(--doc-ink)]'       => $current === null,
                        'text-[var(--doc-muted)] hover:text-[var(--doc-ink)]' => $current !== null,
                    ])
                >Começar aqui</a>

                @foreach ($groups as $group => $names)
                    <div>
                        <p class="px-3 text-[11px] font-semibold tracking-[0.1em] text-[var(--doc-faint)] uppercase">
                            {{ $group }}
                        </p>

                        <div class="mt-1.5 flex flex-col gap-0.5">
                            @foreach ($names as $name)
                                <a
                                    href="{{ $link($name) }}"
                                    @class([
                                        'rounded-lg px-3 py-1.5 text-sm transition-colors duration-(--duration-fast) ease-(--ease-fluid) hover:bg-[var(--doc-surface)] hover:text-[var(--doc-ink)]',
                                        'bg-[var(--doc-surface)] font-medium text-[var(--doc-ink)]' => $current === $name,
                                        'text-[var(--doc-muted)]'                                   => $current !== $name,
                                    ])
                                    @if ($current === $name) aria-current="page" @endif
                                >{{ $titleOf[$name] ?? $name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        <main class="min-w-0 py-10 lg:py-14">{{ $slot }}</main>
    </div>

    {{-- Dev-only page, so its one behaviour rides inline instead of entering the site bundle. --}}
    <script>
        document.querySelectorAll('[data-copy]').forEach((button) => {
            button.addEventListener('click', async () => {
                await navigator.clipboard.writeText(button.closest('div').nextElementSibling.textContent);

                button.textContent = 'Copiado';

                setTimeout(() => (button.textContent = 'Copiar'), 1500);
            });
        });
    </script>
</body>
</html>
