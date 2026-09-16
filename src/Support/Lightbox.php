<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Lightbox
{
    /** The source types fsLightbox renders. Anything else is left for it to detect from the URL. */
    private const array TYPES = ['image', 'video', 'youtube'];

    /**
     * The gallery a slide or item joins. `lightbox` with no value is `true`, and every such
     * component of the same kind shares one gallery under the default name.
     */
    public static function group(mixed $lightbox, string $default): string
    {
        return is_string($lightbox) && trim($lightbox) !== '' ? trim($lightbox) : $default;
    }

    public static function type(mixed $type): ?string
    {
        $normalized = is_string($type) ? strtolower(trim($type)) : null;

        return in_array($normalized, self::TYPES, true) ? $normalized : null;
    }
}
