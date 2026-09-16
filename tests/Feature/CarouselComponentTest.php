<?php

declare(strict_types = 1);

const CAROUSEL = <<<'BLADE'
<x-ui.carousel
    label="Galeria"
    :per-view="['base' => 2, 'md' => 3, 'lg' => 4]"
    :gap="['base' => 12, 'lg' => 32]"
    autoplay="1800"
    pagination
>
    <x-ui.carousel-slide>Um</x-ui.carousel-slide>
    <x-ui.carousel-slide>Dois</x-ui.carousel-slide>
</x-ui.carousel>
BLADE;

/**
 * @return array<string, mixed>
 */
function carouselConfig(string $rendered): array
{
    preg_match('/data-carousel="([^"]*)"/', $rendered, $matches);

    return json_decode(html_entity_decode($matches[1] ?? ''), true) ?? [];
}

it('wraps the slides in the structure swiper expects', function (): void {
    $rendered = (string) $this->blade(CAROUSEL);

    expect($rendered)->toContain('class="swiper"')
        ->toContain('class="swiper-wrapper"')
        ->and(substr_count($rendered, 'swiper-slide'))->toBe(2)
        ->and($rendered)->toContain('Um')
        ->toContain('Dois');
});

it('announces itself as a carousel region', function (): void {
    $this->blade(CAROUSEL)
        ->assertSee('role="region"', false)
        ->assertSee('aria-roledescription="carousel"', false)
        ->assertSee('aria-label="Galeria"', false);
});

it('turns the breakpoint maps into swiper breakpoints', function (): void {
    $config = carouselConfig((string) $this->blade(CAROUSEL));

    expect($config['slidesPerView'])->toBe(2)
        ->and($config['spaceBetween'])->toBe(12)
        ->and($config['breakpoints'])->toBe([
            '768'  => ['slidesPerView' => 3],
            '1024' => ['slidesPerView' => 4, 'spaceBetween' => 32],
        ])
        ->and($config['autoplay'])->toBe(1800);
});

it('takes a single value for perView and gap', function (): void {
    $config = carouselConfig((string) $this->blade(<<<'BLADE'
        <x-ui.carousel :per-view="1" :gap="24">
            <x-ui.carousel-slide>Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE));

    expect($config['slidesPerView'])->toBe(1)
        ->and($config['spaceBetween'])->toBe(24)
        ->and($config['breakpoints'])->toBe([]);
});

it('leaves loop undecided so the script can count the slides', function (): void {
    expect(carouselConfig((string) $this->blade(CAROUSEL))['loop'])->toBeNull();
});

it('honours an explicit loop', function (): void {
    $config = carouselConfig((string) $this->blade(<<<'BLADE'
        <x-ui.carousel :loop="false">
            <x-ui.carousel-slide>Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE));

    expect($config['loop'])->toBeFalse();
});

it('renders the pagination outside the swiper container', function (): void {
    $rendered = (string) $this->blade(CAROUSEL);

    $swiperEnd = strrpos($rendered, '</div>', -(strlen($rendered) - strpos($rendered, 'swiper-pagination')));

    expect($rendered)->toContain('swiper-pagination')
        ->and($swiperEnd)->toBeLessThan(strpos($rendered, 'swiper-pagination'));
});

it('wraps the pagination in its own positioning box', function (): void {
    $rendered = (string) $this->blade(CAROUSEL);

    /** Swiper's own stylesheet makes the strip `position: absolute`. Without a
     *  positioned parent it anchors to the page instead of sitting under the slides.
     *  The box is what matters here, not the spacing it happens to carry. */
    expect($rendered)
        ->toMatch('/<div class="relative [^"]*">\s*<div class="swiper-pagination">/');
});

it('leaves out the arrows and the bullets when they are not asked for', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel>
            <x-ui.carousel-slide>Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($rendered)->not->toContain('swiper-pagination')
        ->not->toContain('swiper-button-next')
        ->and(carouselConfig($rendered)['autoplay'])->toBeFalse();
});

it('adds the arrows when navigation is on', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel navigation>
            <x-ui.carousel-slide>Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($rendered)->toContain('swiper-button-prev')
        ->toContain('swiper-button-next')
        ->and(carouselConfig($rendered)['navigation'])->toBeTrue();
});

it('wraps a slide in a lightbox anchor when the carousel asks for one', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel lightbox="obras">
            <x-ui.carousel-slide source="/grande.jpg">
                <img src="/thumb.jpg" alt="Obra" />
            </x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($rendered)->toContain('href="/grande.jpg"')
        ->toContain('data-fslightbox="obras"')
        ->toContain('src="/thumb.jpg"');
});

it('names the gallery carousel when lightbox comes without a value', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel lightbox>
            <x-ui.carousel-slide source="/grande.jpg">Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($rendered)->toContain('data-fslightbox="carousel"');
});

it('carries the source type through to the anchor', function (): void {
    $rendered = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel lightbox="obras">
            <x-ui.carousel-slide source="https://youtu.be/abc" type="youtube">Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($rendered)->toContain('data-type="youtube"');
});

it('leaves a slide alone without a lightbox or without a source', function (): void {
    /** fsLightbox warns and skips an anchor with no `href`, so there is no anchor to make. */
    $sourceOnly = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel>
            <x-ui.carousel-slide source="/grande.jpg">Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    $lightboxOnly = (string) $this->blade(<<<'BLADE'
        <x-ui.carousel lightbox="obras">
            <x-ui.carousel-slide>Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE);

    expect($sourceOnly)->not->toContain('data-fslightbox')
        ->and($lightboxOnly)->not->toContain('data-fslightbox')
        ->not->toContain('<a');
});

it('keeps the caller classes on the root', function (): void {
    $this->blade(<<<'BLADE'
        <x-ui.carousel class="px-12">
            <x-ui.carousel-slide class="rounded-lg">Um</x-ui.carousel-slide>
        </x-ui.carousel>
    BLADE)
        ->assertSee('class="px-12"', false)
        ->assertSee('swiper-slide h-auto rounded-lg', false);
});

it('leaves auto height off unless it is asked for', function (): void {
    /** A row of cards wants one height for the lot; that is the common case and the default. */
    expect(carouselConfig((string) $this->blade(CAROUSEL))['autoHeight'])->toBeFalse();
});

it('turns auto height on through the prop', function (): void {
    $rendered = (string) $this->blade('<x-ui.carousel auto-height><x-ui.carousel-slide>Um</x-ui.carousel-slide></x-ui.carousel>');

    expect(carouselConfig($rendered)['autoHeight'])->toBeTrue();
});

it('leaves dynamic bullets off unless they are asked for', function (): void {
    expect(carouselConfig((string) $this->blade(CAROUSEL))['dynamicBullets'])->toBeFalse();
});

it('turns the bullet strip on when dynamic bullets are asked for alone', function (): void {
    /** Writing `dynamic-bullets` without `pagination` used to describe a strip that never rendered. */
    $rendered = (string) $this->blade('<x-ui.carousel dynamic-bullets><x-ui.carousel-slide>Um</x-ui.carousel-slide></x-ui.carousel>');

    $config = carouselConfig($rendered);

    expect($config['dynamicBullets'])->toBeTrue()
        ->and($config['pagination'])->toBeTrue()
        ->and($rendered)->toContain('swiper-pagination');
});
