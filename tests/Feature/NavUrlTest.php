<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Navigation;
use Illuminate\Http\Request;

it('leaves an anchor bare on the home page', function (): void {
    /**
     * `smooth-anchors.js` eases `href^="#"`. A full address here would navigate instead of
     * scrolling, which is a reload where there used to be a glide.
     */
    app()->instance('request', Request::create(url('/')));

    expect(Navigation::anchored('#servicos'))->toBe('#servicos');
});

it('points an anchor at the home page from anywhere else', function (string $path): void {
    /**
     * A shared menu renders on every page, and its anchors name sections of the home page.
     * Left bare they resolved against whatever was open: on the privacy policy they pointed at
     * a section that is not there, and on a 404 at `/endereco-errado#servicos`.
     */
    app()->instance('request', Request::create(url($path)));

    expect(Navigation::anchored('#servicos'))->toBe(rtrim(url('/'), '/') . '/#servicos');
})->with([
    'outra página'            => ['/politica-de-privacidade'],
    'endereço que não existe' => ['/asdasdsd'],
    'caminho fundo'           => ['/blog/artigo/2026'],
]);

it('matches the address the home page canonicalises to', function (): void {
    /** `https://site/#servicos`, with the slash — the same form the canonical declares. */
    app()->instance('request', Request::create(url('/contato')));

    expect(Navigation::anchored('#x'))->toContain('/#x')->not->toContain('test#x');
});

it('leaves everything that is not an anchor alone', function (?string $url): void {
    app()->instance('request', Request::create(url('/qualquer')));

    expect(Navigation::anchored($url))->toBe($url);
})->with([
    'caminho'  => ['/contato'],
    'absoluto' => ['https://outro.test/pagina'],
    'mailto'   => ['mailto:a@b.test'],
    'vazio'    => [''],
    'nulo'     => [null],
]);
