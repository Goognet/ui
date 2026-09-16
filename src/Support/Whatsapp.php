<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Whatsapp
{
    private const string BASE = 'https://wa.me/';

    private const string DEFAULT_MESSAGE = 'Olá! Vim pelo site e gostaria de mais informações.';

    /**
     * The conversation link. The host is fixed and the number is reduced to digits, so nothing
     * a caller or a config value passes can turn this into a link to somewhere else.
     */
    public static function url(?string $phone = null, ?string $message = null): string
    {
        $countryCode = Phone::digits(config('goognet-ui.whatsapp.country_code', '55'));

        $digits = Phone::digits(self::firstFilled($phone, config('goognet-ui.whatsapp.number')));

        if ($countryCode !== '' && str_starts_with($digits, $countryCode) && in_array(strlen($digits), [12, 13], true)) {
            $digits = substr($digits, strlen($countryCode));
        }

        $text = self::firstFilled($message, config('goognet-ui.whatsapp.message'), self::DEFAULT_MESSAGE);

        return self::BASE . $countryCode . $digits . '?text=' . rawurlencode($text);
    }

    /** Config defaults are often `''` rather than null, so `??` would hand back an empty value. */
    private static function firstFilled(mixed ...$values): string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return '';
    }
}
