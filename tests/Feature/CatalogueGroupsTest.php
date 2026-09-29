<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Catalogue;
use Illuminate\Support\Collection;

/**
 * The index is grouped by the job the reader came to do, which only works while the grouping is
 * complete. A component in no group is a component the index does not link, and with a page per
 * component that is a page nobody can reach.
 */
function groupedNames(): Collection
{
    return collect(Catalogue::groups())->flatten();
}

it('files every catalogued component in a group', function (): void {
    $missing = collect(Catalogue::entries())
        ->pluck('name')
        ->reject(fn (string $name): bool => groupedNames()->contains($name))
        ->values()
        ->all();

    expect($missing)->toBeEmpty();
});

it('files a component in one group only', function (): void {
    $twice = groupedNames()
        ->countBy()
        ->filter(fn (int $count): bool => $count > 1)
        ->keys()
        ->all();

    expect($twice)->toBeEmpty();
});

it('groups no name the catalogue does not have', function (): void {
    /** A rename that missed the group list would otherwise leave a link to a page that is gone. */
    $catalogued = collect(Catalogue::entries())->pluck('name');

    $ghosts = groupedNames()
        ->reject(fn (string $name): bool => $catalogued->contains($name))
        ->values()
        ->all();

    expect($ghosts)->toBeEmpty();
});

it('finds a catalogued entry by name and nothing else', function (): void {
    expect(Catalogue::entry('button'))->toHaveKey('title', 'Button')
        ->and(Catalogue::entry('nao-existe'))->toBeNull();
});
