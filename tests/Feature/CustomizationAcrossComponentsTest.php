<?php

declare(strict_types = 1);

use Goognet\Ui\Ui;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Vite as ViteFacade;

/** The components that read the build manifest only need an address here, not a real build. */
beforeEach(function (): void {
    ViteFacade::clearResolvedInstance();

    app()->instance(Vite::class, new class () extends Vite
    {
        public function asset($asset, $buildDirectory = null): string
        {
            return '/build/' . ltrim((string) $asset, '/');
        }
    });
});

/**
 * One row per component rebuilt on the customisation layers. A component that reaches the list
 * has to honour all three, so the list is the checklist of what is done.
 *
 * `template`: rendered as is. `rootClass`: a class the package puts on the root element, or null
 * when the package leaves the root bare. `override`: a class at the call site that must replace it.
 */
dataset('customisable', [
    'button'           => ['button', '<x-ui.button %s>Ok</x-ui.button>', 'rounded-control', 'rounded-full'],
    'badge'            => ['badge', '<x-ui.badge %s>Novo</x-ui.badge>', 'rounded-full', 'rounded-none'],
    'link'             => ['link', '<x-ui.link href="/" %s>Ok</x-ui.link>', 'text-current', 'text-blue-700'],
    'heading'          => ['heading', '<x-ui.heading %s>T</x-ui.heading>', 'text-neutral-950', 'text-blue-700'],
    'text'             => ['text', '<x-ui.text %s>T</x-ui.text>', 'text-neutral-700', 'text-blue-700'],
    'container'        => ['container', '<x-ui.container %s>T</x-ui.container>', 'max-w-page', 'max-w-full'],
    'accordion'        => ['accordion', '<x-ui.accordion %s>T</x-ui.accordion>', 'border-neutral-200', 'border-blue-700'],
    'tabs'             => ['tabs', '<x-ui.tabs name="t" %s>T</x-ui.tabs>', 'items-end', 'items-start'],
    'modal'            => ['modal', '<x-ui.modal name="m" %s>T</x-ui.modal>', 'max-w-lg', 'max-w-full'],
    'carousel'         => ['carousel', '<x-ui.carousel %s>T</x-ui.carousel>', null, 'p-4'],
    'carousel-slide'   => ['carousel-slide', '<x-ui.carousel-slide %s>T</x-ui.carousel-slide>', 'h-auto', 'h-full'],
    'gallery'          => ['gallery', '<x-ui.gallery %s>T</x-ui.gallery>', 'gap-4', 'gap-10'],
    'gallery-item'     => ['gallery-item', '<x-ui.gallery-item src="https://cdn.example.com/a.jpg" %s />', 'min-w-0', 'min-w-full'],
    'rating'           => ['rating', '<x-ui.rating :value="3" %s />', 'inline-flex', 'block'],
    'brand'            => ['brand', '<x-ui.brand name="Goognet" %s />', 'inline-flex', 'block'],
    'map'              => ['map', '<x-ui.map src="https://www.google.com/maps/embed?pb=1" %s />', 'aspect-video', 'aspect-square'],
    'whatsapp'         => ['whatsapp', '<x-ui.whatsapp %s>Zap</x-ui.whatsapp>', null, 'mt-8'],
    'image'            => ['image', '<x-ui.image src="https://cdn.example.com/a.jpg" %s />', null, 'rounded-full'],
    'breadcrumb'       => ['breadcrumb', '<x-ui.breadcrumb :items="[[\'label\' => \'Home\', \'url\' => \'/\']]" %s />', null, 'mt-4'],
    'menu'             => ['menu', '<x-ui.menu :items="[]" %s />', null, 'mt-8'],
    'navbar'           => ['navbar', '<x-ui.navbar %s>x</x-ui.navbar>', 'sticky', 'fixed'],
    'sidebar'          => ['sidebar', '<x-ui.sidebar label="Seções" :items="[\'a\' => \'A\']" %s />', 'py-10', 'py-20'],
    'footer'           => ['footer', '<x-ui.footer %s />', 'bg-white', 'bg-neutral-50'],
    'megamenu'         => ['megamenu', '<x-ui.megamenu label="Mais" :groups="[]" %s />', null, 'mt-4'],
    'megamenu-panel'   => ['megamenu-panel', '<x-ui.megamenu-panel :groups="[]" %s />', 'rounded-surface', 'rounded-none'],
    'cookie-consent'   => ['cookie-consent', '<x-ui.cookie-consent %s />', 'p-5', 'p-8'],
    'video'            => ['video', '<x-ui.video url="dQw4w9WgXcQ" %s />', 'aspect-video', 'aspect-square'],
    'field'            => ['field', '<x-ui.field label="N" %s>x</x-ui.field>', 'gap-1.5', 'gap-4'],
    'input'            => ['input', '<x-ui.input name="n" label="N" %s />', 'gap-1.5', 'gap-4'],
    'textarea'         => ['textarea', '<x-ui.textarea name="n" label="N" %s />', 'gap-1.5', 'gap-4'],
    'select'           => ['select', '<x-ui.select name="n" label="N" :options="[\'a\' => \'A\']" %s />', 'gap-1.5', 'gap-4'],
    'video-background' => ['video-background', '<x-ui.video-background src="https://cdn.example.com/a.mp4" %s>x</x-ui.video-background>', 'h-svh', 'h-dvh'],
]);

it('lets a class at the call site replace the package class', function (string $component, string $template, ?string $rootClass, string $override): void {
    $html = (string) $this->blade(sprintf($template, 'class="' . $override . '"'));

    expect($html)->toContain($override);

    if ($rootClass !== null) {
        expect($html)->not->toContain($rootClass);
    }
})->with('customisable');

it('takes classes laid over its root part, with the call site still winning', function (string $component, string $template, ?string $rootClass, string $override): void {
    Ui::component($component)->part('base', $override);

    $html = (string) $this->blade(sprintf($template, ''));

    expect($html)->toContain($override);

    if ($rootClass !== null) {
        expect($html)->not->toContain($rootClass);
    }
})->with('customisable');

it('emits one class attribute on the root element, never two', function (string $component, string $template): void {
    $html = (string) $this->blade(sprintf($template, 'class="mt-4"'));

    preg_match('/<[a-z]+\b[^>]*>/', $html, $tag);

    expect(substr_count($tag[0], 'class='))->toBe(1);
})->with('customisable');
