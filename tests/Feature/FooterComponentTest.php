<?php

declare(strict_types = 1);

beforeEach(function (): void {
    config()->set('goognet-ui.company.name', 'Empresa Exemplo');
    config()->set('goognet-ui.company.mail', 'contato@exemplo.com.br');
    config()->set('goognet-ui.company.phone', '(11) 91234-5678');
    config()->set('goognet-ui.agency.name', 'Goognet');
    config()->set('goognet-ui.agency.url', 'https://www.goognet.com.br');
    config()->set('goognet-ui.menu', [['label' => 'Início', 'url' => '/']]);
    config()->set('goognet-ui.social', ['instagram' => '', 'facebook' => '', 'youtube' => '', 'linkedin' => '', 'twitter' => '', 'tiktok' => '']);
});

it('shows the conversion band and hides it on request', function (): void {
    expect((string) $this->blade('<x-ui.footer />'))->toContain('Precisa de um orçamento?')
        ->and((string) $this->blade('<x-ui.footer :callout="false" />'))->not->toContain('Precisa de um orçamento?');
});

it('renders the band action as one anchor, styled by the button component', function (): void {
    /**
     * Interactive content inside `<a>` is invalid HTML, so this cannot be a `<button>` in a
     * link. It used to be an anchor wearing 27 hand-copied button classes; now `x-ui.button`
     * renders the anchor itself and the styling has one owner.
     */
    $rendered = (string) $this->blade('<x-ui.footer />');

    expect($rendered)->toMatch('/<a(?=[^>]*wa\.me)(?=[^>]*class="[^"]*bg-primary)[^>]*>/')
        ->and($rendered)->not->toMatch('/<a\b[^>]*>(?:(?!<\/a>).)*<button/s');
});

it('lists only the networks that are configured', function (): void {
    expect((string) $this->blade('<x-ui.footer />'))->not->toContain('instagram.com');

    config()->set('goognet-ui.social.instagram', 'https://instagram.com/exemplo');

    $rendered = (string) $this->blade('<x-ui.footer />');

    expect($rendered)->toContain('https://instagram.com/exemplo')
        ->and($rendered)->not->toContain('facebook.com');
});

it('takes the navigation from the shared menu', function (): void {
    config()->set('goognet-ui.menu', [
        ['label' => 'Serviços', 'url' => '/servicos'],
        ['label' => 'Sem link', 'children' => [['label' => 'Filho', 'url' => '/filho']]],
    ]);

    $rendered = (string) $this->blade('<x-ui.footer />');

    /** A dropdown parent has no url of its own, so it is not a footer link. */
    expect($rendered)->toContain('/servicos')
        ->and($rendered)->not->toContain('Sem link');
});

it('links the policy only when the route exists', function (): void {
    expect((string) $this->blade('<x-ui.footer />'))->toContain(route('privacy'));
});

it('signs off with the company and the agency', function (): void {
    $rendered = (string) $this->blade('<x-ui.footer />');

    expect($rendered)->toContain('Empresa Exemplo')
        ->toContain((string) now()->year)
        ->toContain('Goognet');
});

it('drops the contact column when nothing is configured', function (): void {
    config()->set('goognet-ui.company.mail', '');
    config()->set('goognet-ui.company.phone', '');

    expect((string) $this->blade('<x-ui.footer />'))->not->toContain('mailto:');
});

it('links the W3C validator at the page it sits on', function (): void {
    $rendered = (string) $this->get(route('privacy'))->getContent();

    expect($rendered)->toContain('https://validator.w3.org/nu/?doc=' . urlencode(route('privacy')))
        ->toContain('W3C Validator');
});

it('keeps the query string out of the validated URL', function (): void {
    /** A tracking parameter is not part of what gets validated, and would fail the fetch. */
    $rendered = (string) $this->get(route('privacy') . '?utm_source=teste')->getContent();

    expect($rendered)->toContain(urlencode(route('privacy')))
        ->and($rendered)->not->toContain('utm_source%3Dteste');
});

it('marks the seal nofollow so it does not pass ranking', function (): void {
    $rendered = (string) $this->blade('<x-ui.footer />');

    expect($rendered)->toMatch('/<a[^>]*validator\.w3\.org[^>]*rel="nofollow noreferrer noopener"/');
});

it('gives the brand a band of its own, above columns of equal width', function (): void {
    /** As a first column it was 473px holding 280px of content — 233px of dead space. */
    config()->set('goognet-ui.social.instagram', 'https://instagram.com/exemplo');

    $rendered = (string) $this->blade('<x-ui.footer />');

    expect($rendered)->toContain('lg:grid-cols-3')
        ->and(strpos($rendered, 'instagram.com'))->toBeLessThan(strpos($rendered, 'Navegação'));
});

it('never repeats the policy link inside the footer', function (): void {
    /** With the policy in the shared menu it appeared under Navegação and Institucional. */
    config()->set('goognet-ui.menu', [['label' => 'Política de privacidade', 'url' => '/politica-de-privacidade']]);

    $rendered = (string) $this->blade('<x-ui.footer />');

    expect(substr_count($rendered, 'Política de privacidade'))->toBe(1);
});

it('drops a column instead of leaving it empty', function (): void {
    config()->set('goognet-ui.menu', []);

    expect((string) $this->blade('<x-ui.footer />'))->not->toContain('Navegação');
});

it('clears the floating button with room below, not a reserve at the side', function (): void {
    /** The button is 64px at 12px from the corner and the last row is the end of the page.
     *  Reserving width on the right made the bar stop short of the columns above it; the
     *  room goes underneath instead, and the button lands on it. Measured at 1280px: no
     *  overlap, and the last row ends 22px above the button. */
    expect((string) $this->blade('<x-ui.footer />'))->toContain('pb-24')
        ->not->toContain('sm:pe-20');
});

it('splits the legal block into two ruled rows', function (): void {
    $rendered = (string) $this->blade('<x-ui.footer />');

    /** Copyright alone on the first row, seal and credit on the second, each row opening
     *  with its own hairline so the pair does not read as one loose clump. */
    expect(substr_count($rendered, 'border-t border-neutral-100'))->toBe(2)
        ->and(strpos($rendered, 'Todos os direitos reservados'))->toBeLessThan(strpos($rendered, 'W3C Validator'));
});

it('offers a way back to the top of the page', function (): void {
    $rendered = (string) $this->blade('<x-ui.footer />');

    /** The empty fragment scrolls to the top of the document, and `scroll-smooth` on the
     *  html element animates it. No script involved. */
    expect($rendered)->toContain('Voltar ao topo')
        ->toMatch('/<a href="#"[^>]*>/');
});
