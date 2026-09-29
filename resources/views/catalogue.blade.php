@php
    /**
     * The catalogue's entry view: it picks the page and hands it the way to address the others.
     *
     * Two callers render the same two components. Inside a site they are one route with an
     * optional segment, so the links are named routes. Published to GitHub Pages there is no
     * router, so `bin/docs` writes a folder per component and passes relative links instead.
     */
    $link = fn (?string $name): string => $name === null
        ? route('goognet-ui.catalogue')
        : route('goognet-ui.catalogue.component', $name);
@endphp

@if (($component ?? null) === null)
    <x-goognet-ui::catalogue.index :link="$link" />
@else
    <x-goognet-ui::catalogue.component :doc="$component" :link="$link" />
@endif
