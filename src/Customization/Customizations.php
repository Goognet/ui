<?php

declare(strict_types = 1);

namespace Goognet\Ui\Customization;

/** One customisation per component, kept for the lifetime of the application. */
final class Customizations
{
    /** @var array<string, ComponentCustomization> */
    private array $components = [];

    public function for(string $component): ComponentCustomization
    {
        return $this->components[$component] ??= new ComponentCustomization();
    }
}
