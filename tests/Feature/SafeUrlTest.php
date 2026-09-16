<?php

declare(strict_types = 1);

use Goognet\Ui\Support\SafeUrl;
use Illuminate\View\ComponentAttributeBag;

dataset('script urls', [
    'plain'                     => 'javascript:alert(1)',
    'upper case'                => 'JAVASCRIPT:alert(1)',
    'mixed case'                => 'JaVaScRiPt:alert(1)',
    'leading space'             => ' javascript:alert(1)',
    'leading null byte'         => "\x00javascript:alert(1)",
    'leading control character' => "\x1Fjavascript:alert(1)",
    'tab inside the scheme'     => "java\tscript:alert(1)",
    'newline inside the scheme' => "java\nscript:alert(1)",
    'carriage return inside'    => "java\rscript:alert(1)",
    'vbscript'                  => 'vbscript:msgbox(1)',
    'html data url'             => 'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
    'svg data url'              => 'data:image/svg+xml,<svg onload=alert(1)>',
    'file'                      => 'file:///etc/passwd',
]);

dataset('safe links', [
    'https'              => ['https://goognet.com.br/contato', 'https://goognet.com.br/contato'],
    'http'               => ['http://exemplo.com', 'http://exemplo.com'],
    'mailto'             => ['mailto:contato@exemplo.com', 'mailto:contato@exemplo.com'],
    'tel'                => ['tel:+5511912345678', 'tel:+5511912345678'],
    'absolute path'      => ['/politica-de-privacidade', '/politica-de-privacidade'],
    'relative path'      => ['contato', 'contato'],
    'parent path'        => ['../contato', '../contato'],
    'fragment'           => ['#servicos', '#servicos'],
    'query'              => ['?pagina=2', '?pagina=2'],
    'protocol relative'  => ['//cdn.exemplo.com/a.js', '//cdn.exemplo.com/a.js'],
    'surrounding spaces' => ['  https://exemplo.com  ', 'https://exemplo.com'],
    'upper case scheme'  => ['HTTPS://exemplo.com', 'HTTPS://exemplo.com'],
]);

it('refuses a script url as a link', function (string $url): void {
    expect(SafeUrl::href($url))->toBeNull();
})->with('script urls');

it('refuses a script url as media', function (string $url): void {
    expect(SafeUrl::media($url))->toBeNull();
})->with(['javascript:alert(1)', "java\tscript:alert(1)", 'vbscript:msgbox(1)', 'data:text/html,<script>alert(1)</script>', 'file:///etc/passwd']);

it('refuses a script url as a frame', function (string $url): void {
    expect(SafeUrl::frame($url))->toBeNull();
})->with('script urls');

it('keeps a safe link as it came, trimmed', function (string $url, string $expected): void {
    expect(SafeUrl::href($url))->toBe($expected);
})->with('safe links');

it('accepts an image data url as media and never as a link', function (string $url): void {
    expect(SafeUrl::media($url))->toBe($url)
        ->and(SafeUrl::href($url))->toBeNull();
})->with([
    'data:image/png;base64,iVBORw0KGgo=',
    'data:image/jpeg;base64,/9j/4AAQ',
    'data:image/webp;base64,UklGRg==',
    'data:image/svg+xml,%3Csvg%3E%3C/svg%3E',
]);

it('frames only absolute https with a host', function (): void {
    expect(SafeUrl::frame('https://www.google.com/maps/embed?pb=1'))->toBe('https://www.google.com/maps/embed?pb=1')
        ->and(SafeUrl::frame('http://www.google.com/maps/embed'))->toBeNull()
        ->and(SafeUrl::frame('/mapa'))->toBeNull()
        ->and(SafeUrl::frame('//www.google.com/maps'))->toBeNull()
        ->and(SafeUrl::frame('https:///sem-host'))->toBeNull();
});

it('answers null for anything that is not a string', function (mixed $url): void {
    expect(SafeUrl::href($url))->toBeNull()
        ->and(SafeUrl::media($url))->toBeNull()
        ->and(SafeUrl::frame($url))->toBeNull();
})->with([
    'null'  => [null],
    'empty' => [''],
    'blank' => ['   '],
    'int'   => [42],
    'array' => [['https://exemplo.com']],
]);

it('reads the allowed schemes from the config', function (): void {
    config()->set('goognet-ui.security.link_schemes', ['https']);

    expect(SafeUrl::href('https://exemplo.com'))->toBe('https://exemplo.com')
        ->and(SafeUrl::href('mailto:a@b.com'))->toBeNull()
        ->and(SafeUrl::href('/relativo'))->toBe('/relativo');
});

it('filters url attributes passed through the bag, not only the props', function (): void {
    $bag = SafeUrl::attributes(new ComponentAttributeBag([
        'formaction'    => 'javascript:alert(1)',
        'xlink:href'    => "java\tscript:alert(1)",
        'src'           => 'data:text/html,<script>alert(1)</script>',
        'poster'        => 'https://exemplo.com/capa.jpg',
        'srcdoc'        => '<p>qualquer coisa</p>',
        'class'         => 'mt-4',
        'data-carousel' => 'javascript:nao-e-url',
    ]));

    expect($bag->getAttributes())->toBe([
        'poster'        => 'https://exemplo.com/capa.jpg',
        'class'         => 'mt-4',
        'data-carousel' => 'javascript:nao-e-url',
    ]);
});

it('matches url attribute names regardless of case', function (): void {
    $bag = SafeUrl::attributes(new ComponentAttributeBag(['FormAction' => 'javascript:alert(1)']));

    expect($bag->getAttributes())->toBeEmpty();
});
