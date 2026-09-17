<?php

declare(strict_types = 1);

namespace Goognet\Ui;

use BadMethodCallException;
use Goognet\Ui\Customization\ComponentCustomization;
use Goognet\Ui\Customization\Customizations;
use Illuminate\Support\Str;

/**
 * Customise a component for this site: `Ui::button()->variant('outline', '...')`.
 *
 * The method is the component's name in camel case — `Ui::carouselSlide()` is `carousel-slide` —
 * and asking for a component the package does not have is an error, so a typo cannot silently
 * customise nothing.
 *
 * @method static ComponentCustomization button()
 */
final class Ui
{
    /**
     * @param  array<int, mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): ComponentCustomization
    {
        return self::component(Str::kebab($method));
    }

    public static function component(string $name): ComponentCustomization
    {
        if (preg_match('/^[a-z0-9-]+$/', $name) !== 1 || ! is_file(__DIR__ . '/../resources/views/components/' . $name . '.blade.php')) {
            throw new BadMethodCallException('goognet/ui has no component named "' . $name . '".');
        }

        return app(Customizations::class)->for($name);
    }
}
