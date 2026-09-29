<?php

declare(strict_types = 1);

use Goognet\Ui\Support\BoostSkill;
use Goognet\Ui\Support\Catalogue;
use Goognet\Ui\Support\ComponentProps;

/**
 * The Boost integration, checked at the source.
 *
 * What these prove: the guideline and the skill sit where Boost looks for them, the skill is a
 * projection of the catalogue rather than a second copy, and what is committed matches what the
 * generator writes today.
 *
 * What they do not prove: that Boost copies them into a site. That is Boost's own behaviour,
 * exercised by `ThirdPartyPackage::boostDirectories()`, and it only needs the paths to be right.
 */
function skillPath(string $path = ''): string
{
    return dirname(__DIR__, 2) . '/' . BoostSkill::DIRECTORY . ($path === '' ? '' : '/' . $path);
}

it('puts the guideline where boost looks for a third-party package', function (): void {
    /**
     * `PackageRegistry::boostPath()` joins the package root with `resources/boost/<subpath>` and
     * returns null when the directory is absent, so the whole integration is this path.
     */
    $guideline = dirname(__DIR__, 2) . '/resources/boost/guidelines/core.blade.php';

    expect($guideline)->toBeFile()
        ->and(file_get_contents($guideline))->toContain('GuidelineAssist');
});

it('keeps the guideline short, because it is inlined into every prompt', function (): void {
    /**
     * A guideline is composed into the project's CLAUDE.md, so its whole length is paid for on
     * every message, in conversations that never touch a view. The component documentation is a
     * skill for this reason. 120 lines is roughly twice what it takes today: room to grow, not
     * room to move the catalogue in.
     */
    $lines = count(file(dirname(__DIR__, 2) . '/resources/boost/guidelines/core.blade.php') ?: []);

    expect($lines)->toBeLessThanOrEqual(120);
});

it('points the guideline at the skill instead of repeating it', function (): void {
    expect(file_get_contents(dirname(__DIR__, 2) . '/resources/boost/guidelines/core.blade.php'))
        ->toContain('skill `goognet-ui`');
});

it('ships a skill page for every catalogued component', function (): void {
    $missing = collect(Catalogue::entries())
        ->pluck('name')
        ->reject(fn (string $name): bool => is_file(skillPath('components/' . $name . '.md')))
        ->values()
        ->all();

    expect($missing)->toBeEmpty();
});

it('leaves no page behind for a component the catalogue dropped', function (): void {
    $catalogued = collect(Catalogue::entries())->pluck('name');

    $orphans = collect(glob(skillPath('components/*.md')) ?: [])
        ->map(fn (string $path): string => basename($path, '.md'))
        ->reject(fn (string $name): bool => $catalogued->contains($name))
        ->values()
        ->all();

    expect($orphans)->toBeEmpty();
});

it('has the committed skill match what the generator writes', function (): void {
    /**
     * The whole point of generating it. Editing a component and forgetting the docs is the bug
     * this replaces: here the docs cannot be edited, only regenerated with `php bin/skill`.
     */
    $stale = collect(BoostSkill::files())
        ->reject(function (string $expected, string $path): bool {
            $committed = is_file(skillPath($path)) ? file_get_contents(skillPath($path)) : null;

            return $committed === $expected;
        })
        ->keys()
        ->all();

    expect($stale)->toBeEmpty('Rode `php bin/skill` e faça commit do resultado.');
});

it('names the variants and sizes a component accepts', function (): void {
    /**
     * A table saying `variant` takes a string sends the reader to the source. An unknown name
     * falls back to the default silently, so the screen does not answer it either.
     */
    expect(ComponentProps::options('button'))
        ->toBe([
            'variants' => ['default', 'primary', 'secondary', 'outline', 'filled', 'ghost'],
            'sizes'    => ['xs', 'sm', 'base', 'lg'],
        ]);

    expect(file_get_contents(skillPath('components/button.md')))
        ->toContain('`default`, `primary`, `secondary`, `outline`, `filled`, `ghost`');
});

it('reads a component with no variants without inventing any', function (): void {
    expect(ComponentProps::options('container'))->toBe(['variants' => [], 'sizes' => []])
        ->and(ComponentProps::options('nao-existe'))->toBe(['variants' => [], 'sizes' => []]);
});

it('writes the skill as markdown, not as the catalogue html', function (): void {
    /** The catalogue prose is HTML because it renders into a page; a skill is read as markdown. */
    $pages = collect(glob(skillPath('components/*.md')) ?: [])
        ->mapWithKeys(fn (string $path): array => [basename($path) => (string) file_get_contents($path)]);

    $withTags = $pages
        ->filter(fn (string $body): bool => preg_match('~</(code|strong|em)>~', $body) === 1)
        ->keys()
        ->all();

    expect($withTags)->toBeEmpty();
});

it('declares the skill frontmatter boost and claude code both read', function (): void {
    $index = (string) file_get_contents(skillPath('SKILL.md'));

    expect($index)->toStartWith("---\nname: goognet-ui\n")
        ->and($index)->toContain('description: "')
        ->and($index)->toContain('author: goognet');
});
