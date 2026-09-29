<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The markup the package emits, checked at the source.
 *
 * `<img … />` is valid HTML5 and the W3C validator reports it as Info, not an error. It is still
 * not what this package prints: the sites built on it run the validator as a badge in their
 * footer, and a page that reports nothing reads better than a page that reports two notes about
 * a slash.
 *
 * This regressed once and quietly. `goognet/blade-pint` sets Prettier's `bladeVoidElementSlash`
 * to `never` on Pint's own configuration, and it was installed in the sites but not here, so
 * Pint — the last tool to write these files — put every terminator back.
 */
it('prints html void elements without the xhtml terminator', function (): void {
    /** The HTML void elements. SVG's `<path />` and `<circle />` are not among them and must stay. */
    $void = 'area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr';

    $offenders = collect(File::allFiles(__DIR__ . '/../../resources/views'))
        ->filter(fn (SplFileInfo $file): bool => str_ends_with($file->getFilename(), '.blade.php'))
        ->mapWithKeys(function (SplFileInfo $file) use ($void): array {
            preg_match_all('~<(' . $void . ')\b[^>]*?/>~is', $file->getContents(), $found);

            return $found[0] === [] ? [] : [$file->getRelativePathname() => $found[1]];
        })
        ->all();

    expect($offenders)->toBeEmpty('Rode `vendor/bin/pint` com goognet/blade-pint instalado; o cache do Pint esconde arquivos já formatados.');
});

it('keeps goognet/blade-pint installed, since Pint undoes this without it', function (): void {
    /**
     * Pint reads its own Prettier configuration from its vendor directory, not the project's
     * `.prettierrc`, so the repository's own setting does not reach it. The package is what
     * bridges that, and dropping it would revert every file above on the next format.
     */
    $composer = json_decode((string) file_get_contents(__DIR__ . '/../../composer.json'), true);

    expect($composer['require-dev'] ?? [])->toHaveKey('goognet/blade-pint')
        ->and($composer['config']['allow-plugins'] ?? [])->toHaveKey('goognet/blade-pint');
});

it('leaves the self-closing tags svg needs alone', function (): void {
    /** A guard on the guard: `<path />` inside the button's spinner is required, not a leftover. */
    $spinner = (string) file_get_contents(__DIR__ . '/../../resources/views/components/button.blade.php');

    expect($spinner)->toContain('<circle class="opacity-25"')
        ->and($spinner)->toMatch('~<path [^>]*/>~');
});
