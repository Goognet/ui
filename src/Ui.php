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
 * @method static ComponentCustomization accordion()
 * @method static ComponentCustomization accordionItem()
 * @method static ComponentCustomization alert()
 * @method static ComponentCustomization badge()
 * @method static ComponentCustomization brand()
 * @method static ComponentCustomization breadcrumb()
 * @method static ComponentCustomization button()
 * @method static ComponentCustomization card()
 * @method static ComponentCustomization carousel()
 * @method static ComponentCustomization carouselSlide()
 * @method static ComponentCustomization checkbox()
 * @method static ComponentCustomization container()
 * @method static ComponentCustomization cookieConsent()
 * @method static ComponentCustomization counter()
 * @method static ComponentCustomization dropdown()
 * @method static ComponentCustomization field()
 * @method static ComponentCustomization footer()
 * @method static ComponentCustomization gallery()
 * @method static ComponentCustomization galleryItem()
 * @method static ComponentCustomization heading()
 * @method static ComponentCustomization image()
 * @method static ComponentCustomization input()
 * @method static ComponentCustomization link()
 * @method static ComponentCustomization map()
 * @method static ComponentCustomization megamenu()
 * @method static ComponentCustomization megamenuPanel()
 * @method static ComponentCustomization menu()
 * @method static ComponentCustomization modal()
 * @method static ComponentCustomization navbar()
 * @method static ComponentCustomization pagination()
 * @method static ComponentCustomization radio()
 * @method static ComponentCustomization rating()
 * @method static ComponentCustomization select()
 * @method static ComponentCustomization sidebar()
 * @method static ComponentCustomization tab()
 * @method static ComponentCustomization table()
 * @method static ComponentCustomization tabs()
 * @method static ComponentCustomization text()
 * @method static ComponentCustomization textarea()
 * @method static ComponentCustomization toast()
 * @method static ComponentCustomization tooltip()
 * @method static ComponentCustomization video()
 * @method static ComponentCustomization videoBackground()
 * @method static ComponentCustomization whatsapp()
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
