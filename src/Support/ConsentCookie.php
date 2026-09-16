<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class ConsentCookie
{
    public const string DEFAULT = 'cookie_consent';

    /**
     * The browser writes this name straight into `document.cookie`, where a `;` or an `=` would
     * add attributes of its own. Anything outside a plain token falls back to the default.
     */
    public static function name(mixed $name = null): string
    {
        $name ??= config('goognet-ui.cookie_consent.name');

        return is_string($name) && preg_match('/^[A-Za-z0-9_-]+$/', $name) === 1 ? $name : self::DEFAULT;
    }
}
