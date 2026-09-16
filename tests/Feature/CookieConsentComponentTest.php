<?php

declare(strict_types = 1);

it('shows the banner to a visitor who has not accepted yet', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-cookie-consent', false)
        ->assertSee('Aceitar', false);
});

it('never sends the markup once the cookie is there', function (): void {
    /** Server side, so the banner does not flash before the script runs. */
    $this->withUnencryptedCookie('cookie_consent', 'accepted')
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('data-cookie-consent', false);
});

it('ignores a cookie that says anything else', function (): void {
    $this->withUnencryptedCookie('cookie_consent', 'recusado')
        ->get(route('home'))
        ->assertSee('data-cookie-consent', false);
});

it('points at the privacy policy', function (): void {
    $this->get(route('home'))
        ->assertSee(route('privacy'), false)
        ->assertSee('Saber mais', false);
});

it('takes its own cookie name', function (): void {
    $rendered = (string) $this->blade('<x-ui.cookie-consent name="aviso_lgpd" />');

    expect($rendered)->toContain('data-cookie-consent="aviso_lgpd"')
        ->toContain('id="aviso_lgpd-title"');
});

it('takes its own copy through the slot', function (): void {
    $this->blade('<x-ui.cookie-consent>Este site usa cookies.</x-ui.cookie-consent>')
        ->assertSee('Este site usa cookies.', false)
        ->assertDontSee('Usamos cookies para fazer o site funcionar', false);
});

it('announces itself as a dialog', function (): void {
    $rendered = (string) $this->blade('<x-ui.cookie-consent />');

    expect($rendered)->toContain('role="dialog"')
        ->toContain('aria-labelledby="cookie_consent-title"');
});

it('is a bar on the bottom edge and a corner card from sm up', function (): void {
    $rendered = (string) $this->blade('<x-ui.cookie-consent />');

    /** Measured at 390px: the bar spans the full width and touches the bottom, and the
     *  floating button rests 12px above it. At 1280px: card 448px wide at 20px from the
     *  corner, 728px clear of the button. No overlap either way. */
    expect($rendered)->toContain('inset-x-0')
        ->toContain('bottom-0')
        ->toContain('rounded-t-2xl')
        ->toContain('sm:inset-x-auto')
        ->toContain('sm:bottom-5')
        ->toContain('sm:left-5')
        ->toContain('sm:rounded-2xl');
});

it('leads with the accept action and keeps the policy beside it', function (): void {
    $rendered = (string) $this->blade('<x-ui.cookie-consent />');

    expect($rendered)->toContain('data-cookie-accept')
        ->and(strpos($rendered, 'Saber mais'))->toBeLessThan(strpos($rendered, 'Aceitar'));
});
