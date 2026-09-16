<?php

declare(strict_types = 1);

use Goognet\Ui\Support\ConsentCookie;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Vite as ViteFacade;

beforeEach(function (): void {
    ViteFacade::clearResolvedInstance();

    app()->instance(Vite::class, new class () extends Vite
    {
        public function asset($asset, $buildDirectory = null): string
        {
            return '/' . ltrim((string) $asset, '/');
        }
    });
});

/**
 * Every component that takes a URL, rendered with an attack in each place the URL can arrive.
 * A component ported to the package adds its lines here: the file is the checklist of what is
 * guarded, so a new `href` that skips SafeUrl shows up as a missing case, not as a quiet hole.
 */
dataset('attacks', [
    'button href'        => '<x-ui.button href="javascript:alert(1)">x</x-ui.button>',
    'button upper case'  => '<x-ui.button href="JAVASCRIPT:alert(1)">x</x-ui.button>',
    'button tab'         => "<x-ui.button href=\"java\tscript:alert(1)\">x</x-ui.button>",
    'button formaction'  => '<x-ui.button type="submit" formaction="javascript:alert(1)">x</x-ui.button>',
    'button xlink:href'  => '<x-ui.button xlink:href="javascript:alert(1)">x</x-ui.button>',
    'button srcdoc'      => '<x-ui.button srcdoc="<script>alert(1)</script>">x</x-ui.button>',
    'link href'          => '<x-ui.link href="javascript:alert(1)">x</x-ui.link>',
    'link tab'           => "<x-ui.link href=\"java\tscript:alert(1)\">x</x-ui.link>",
    'link formaction'    => '<x-ui.link formaction="javascript:alert(1)">x</x-ui.link>',
    'badge href'         => '<x-ui.badge href="javascript:alert(1)">x</x-ui.badge>',
    'badge xlink:href'   => '<x-ui.badge xlink:href="javascript:alert(1)">x</x-ui.badge>',
    'image src'          => '<x-ui.image src="javascript:alert(1)" alt="" />',
    'image html data'    => '<x-ui.image src="data:text/html,<script>alert(1)</script>" alt="" />',
    'heading attribute'  => '<x-ui.heading xlink:href="javascript:alert(1)">x</x-ui.heading>',
    'text attribute'     => '<x-ui.text formaction="javascript:alert(1)">x</x-ui.text>',
    'container srcdoc'   => '<x-ui.container srcdoc="<script>alert(1)</script>">x</x-ui.container>',
    'carousel slide'     => '<x-ui.carousel lightbox><x-ui.carousel-slide source="javascript:alert(1)">x</x-ui.carousel-slide></x-ui.carousel>',
    'carousel slide tab' => "<x-ui.carousel lightbox><x-ui.carousel-slide source=\"java\tscript:alert(1)\">x</x-ui.carousel-slide></x-ui.carousel>",
    'gallery source'     => '<x-ui.gallery lightbox><x-ui.gallery-item src="https://exemplo.com/a.jpg" source="javascript:alert(1)" /></x-ui.gallery>',
    'gallery src'        => '<x-ui.gallery lightbox><x-ui.gallery-item src="javascript:alert(1)" /></x-ui.gallery>',
    'modal attribute'    => '<x-ui.modal name="m" formaction="javascript:alert(1)">x</x-ui.modal>',
    'accordion attr'     => '<x-ui.accordion xlink:href="javascript:alert(1)"><x-ui.accordion-item label="a">b</x-ui.accordion-item></x-ui.accordion>',
    'tabs attribute'     => '<x-ui.tabs name="t" formaction="javascript:alert(1)"><x-ui.tab label="a">b</x-ui.tab></x-ui.tabs>',
    'rating attribute'   => '<x-ui.rating :value="3" xlink:href="javascript:alert(1)" />',
    'map src'            => '<x-ui.map src="javascript:alert(1)" />',
    'map srcdoc'         => '<x-ui.map src="https://www.google.com/maps/embed?pb=1" srcdoc="<script>alert(1)</script>" />',
    'video attribute'    => '<x-ui.video url="dQw4w9WgXcQ" formaction="javascript:alert(1)" />',
    'video background'   => '<x-ui.video-background src="https://exemplo.com/v.mp4" xlink:href="javascript:alert(1)" />',
]);

it('never renders an executable url', function (string $template): void {
    $html = preg_replace('/[\t\n\r]/', '', (string) $this->blade($template));

    expect(strtolower((string) $html))
        ->not->toContain('javascript:')
        ->not->toContain('srcdoc');
})->with('attacks');

it('still renders the element when its url is refused', function (): void {
    $html = (string) $this->blade('<x-ui.button href="javascript:alert(1)">Enviar</x-ui.button>');

    expect($html)->toContain('<a')
        ->toContain('Enviar')
        ->not->toContain('href=');
});

it('keeps an image path from climbing out of the project', function (): void {
    expect((string) $this->blade('<x-ui.image src="../../.env" alt="" />'))->not->toContain('<img')
        ->and((string) $this->blade('<x-ui.image src="resources/../../.env" alt="" />'))->not->toContain('<img');
});

it('refuses an html data url for an image but keeps an image one', function (): void {
    expect((string) $this->blade('<x-ui.image src="data:text/html,<p>x</p>" alt="" />'))->not->toContain('<img')
        ->and((string) $this->blade('<x-ui.image src="data:image/png;base64,iVBORw0KGgo=" alt="" />'))
        ->toContain('src="data:image/png;base64,iVBORw0KGgo="');
});

it('closes the faq schema script even when an answer tries to', function (): void {
    $html = (string) $this->blade('<x-ui.accordion :faq="[\'P\' => \'</script><script>alert(1)</script>\']" />');

    expect(substr_count($html, '</script>'))->toBe(1)
        ->and($html)->not->toContain('<script>alert(1)');
});

it('only passes a lightbox type fsLightbox knows', function (): void {
    $html = (string) $this->blade(
        '<x-ui.gallery lightbox><x-ui.gallery-item src="https://exemplo.com/a.jpg" :type="$type" /></x-ui.gallery>',
        ['type' => 'custom" onmouseover="alert(1)'],
    );

    expect($html)->not->toContain('data-type')
        ->not->toContain('onmouseover');
});

it('caps a rating so a huge max cannot blow up the page', function (): void {
    $html = (string) $this->blade('<x-ui.rating :value="3" :max="1000000" />');

    expect(substr_count($html, '<svg'))->toBe(20);
});

it('frames only the hosts it was told to trust', function (): void {
    expect((string) $this->blade('<x-ui.map src="https://phishing.example/login" />'))->not->toContain('<iframe')
        ->and((string) $this->blade('<x-ui.map src="http://www.google.com/maps/embed?pb=1" />'))->not->toContain('<iframe')
        ->and((string) $this->blade('<x-ui.map src="https://www.google.com/maps/embed?pb=1" />'))->toContain('<iframe');
});

it('sandboxes the map so a click inside it cannot move the page', function (): void {
    $html = (string) $this->blade('<x-ui.map src="https://www.google.com/maps/embed?pb=1" />');

    expect($html)->toContain('sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"')
        ->not->toContain('allow-top-navigation');
});

it('frames any https host once the allowlist is emptied', function (): void {
    config()->set('goognet-ui.security.frame_hosts', []);

    expect((string) $this->blade('<x-ui.map src="https://mapas.exemplo.com/embed" />'))->toContain('<iframe');
});

it('points a video link at youtube whatever host the address named', function (): void {
    $html = (string) $this->blade('<x-ui.video url="https://evil.example/?v=dQw4w9WgXcQ" :lightbox="false" />');

    expect($html)->toContain('href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"')
        ->not->toContain('evil.example');
});

dataset('navigation attacks', [
    'script'   => ['javascript:alert(1)'],
    'tab'      => ["java\tscript:alert(1)"],
    'vbscript' => ['vbscript:msgbox(1)'],
]);

it('refuses a script url in any level of the menu', function (string $url): void {
    $html = (string) $this->blade('<x-ui.menu :items="$items" />', ['items' => [
        ['label' => 'Direto', 'url' => $url],
        ['label' => 'Dropdown', 'children' => [['label' => 'Filho', 'url' => $url]]],
        ['label' => 'Mega', 'groups' => [['label' => 'G', 'children' => [['label' => 'Neto', 'url' => $url]]]]],
    ]]);

    expect(strtolower((string) preg_replace('/\s/', '', $html)))->not->toContain('script:')
        ->and($html)->toContain('Direto')->toContain('Filho')->toContain('Neto');
})->with('navigation attacks');

it('refuses a script url in the sidebar and the breadcrumb, schema included', function (string $url): void {
    $sidebar    = (string) $this->blade('<x-ui.sidebar label="A" :items="$items" />', ['items' => [['label' => 'X', 'url' => $url], ['label' => 'Y', 'url' => '#ok']]]);
    $breadcrumb = (string) $this->blade('<x-ui.breadcrumb :items="$items" />', ['items' => [['label' => 'X', 'url' => $url], ['label' => 'Atual']]]);

    expect(strtolower((string) preg_replace('/\s/', '', $sidebar . $breadcrumb)))->not->toContain('script:');
})->with('navigation attacks');

it('refuses script urls that reach the footer through the config', function (string $url): void {
    config()->set('goognet-ui.social.instagram', $url);
    config()->set('goognet-ui.agency', ['name' => 'Agência', 'url' => $url]);
    config()->set('goognet-ui.menu', [['label' => 'Menu', 'url' => $url]]);

    $html = (string) $this->blade('<x-ui.footer />');

    expect(strtolower((string) preg_replace('/\s/', '', $html)))->not->toContain('script:');
})->with('navigation attacks');

it('refuses a script url on the brand link and an html data url as its logo', function (): void {
    $html = (string) $this->blade('<x-ui.brand href="javascript:alert(1)" logo="data:text/html,<script>alert(1)</script>" name="Acme" />');

    expect(strtolower($html))->not->toContain('javascript:')
        ->not->toContain('data:text/html')
        ->and($html)->toContain('Acme');
});

it('refuses a script url as the cookie policy link', function (): void {
    expect(strtolower((string) $this->blade('<x-ui.cookie-consent policy="javascript:alert(1)" />')))->not->toContain('javascript:');
});

it('keeps a whatsapp link on wa.me whatever the phone and message carry', function (): void {
    $html = (string) $this->blade('<x-ui.whatsapp :phone="$phone" :message="$message">Falar</x-ui.whatsapp>', [
        'phone'   => '11 99999-9999" onmouseover="alert(1)',
        'message' => '"><script>alert(1)</script>',
    ]);

    expect($html)->toMatch('#href="https://wa\.me/\d+\?text=[^"<>]*"#')
        ->not->toContain('onmouseover')
        ->not->toContain('<script>');
});

it('exempts the consent cookie from encryption under the configured name', function (): void {
    expect(EncryptCookies::class)->toBeString()
        ->and(new ReflectionProperty(EncryptCookies::class, 'neverEncrypt')->getValue())
        ->toContain('cookie_consent');
});

it('never lets a cookie name carry attributes of its own', function (string $name): void {
    $html = (string) $this->blade('<x-ui.cookie-consent :name="$name" />', ['name' => $name]);

    expect($html)->toContain('data-cookie-consent="cookie_consent"')
        ->and(ConsentCookie::name($name))->toBe('cookie_consent');
})->with([
    'semicolon' => ['aviso; Domain=.outro-site.com'],
    'equals'    => ['aviso=1'],
    'space'     => ['aviso lgpd'],
    'quote'     => ['aviso"x'],
]);

it('keeps a plain cookie name as it came', function (): void {
    expect(ConsentCookie::name('aviso_lgpd-2'))->toBe('aviso_lgpd-2');
});
