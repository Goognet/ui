<?php

declare(strict_types = 1);

namespace Goognet\Ui\Customization;

use Goognet\Ui\Support\ClassList;

/**
 * What one site changes about one component, declared once in a service provider and read by
 * the component on every render. Nothing is copied out of the package, so updating it keeps
 * every customisation in place.
 */
final class ComponentCustomization
{
    /** @var array<string, mixed> */
    private array $defaults = [];

    /** @var array<string, string> */
    private array $variants = [];

    /** @var array<string, string> */
    private array $sizes = [];

    /** @var array<string, array{classes: string, replace: bool}> */
    private array $parts = [];

    /**
     * Props used when a call site does not pass them: `->defaults(['variant' => 'primary'])`.
     *
     * @param  array<string, mixed>  $props
     */
    public function defaults(array $props): self
    {
        $this->defaults = [...$this->defaults, ...$props];

        return $this;
    }

    /** Adds a variant, or replaces the package's variant of the same name. */
    public function variant(string $name, string $classes): self
    {
        $this->variants[$name] = $classes;

        return $this;
    }

    /** Adds a size, or replaces the package's size of the same name. */
    public function size(string $name, string $classes): self
    {
        $this->sizes[$name] = $classes;

        return $this;
    }

    /**
     * Classes laid over a part of the component. A utility here replaces the package's utility
     * for the same property, and anything else is added.
     */
    public function part(string $part, string $classes): self
    {
        $this->parts[$part] = ['classes' => $classes, 'replace' => false];

        return $this;
    }

    /** Classes that replace a part's classes entirely, for a component rebuilt from scratch. */
    public function replacePart(string $part, string $classes): self
    {
        $this->parts[$part] = ['classes' => $classes, 'replace' => true];

        return $this;
    }

    public function default(string $prop, mixed $fallback): mixed
    {
        return array_key_exists($prop, $this->defaults) ? $this->defaults[$prop] : $fallback;
    }

    /**
     * @param  array<string, string>  $package
     * @return array<string, string>
     */
    public function variants(array $package): array
    {
        return [...$package, ...$this->variants];
    }

    /**
     * @param  array<string, string>  $package
     * @return array<string, string>
     */
    public function sizes(array $package): array
    {
        return [...$package, ...$this->sizes];
    }

    public function classes(string $part, string $package): string
    {
        $custom = $this->parts[$part] ?? null;

        if ($custom === null) {
            return $package;
        }

        return $custom['replace'] ? $custom['classes'] : ClassList::merge($package, $custom['classes']);
    }
}
