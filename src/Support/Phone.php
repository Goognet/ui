<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Phone
{
    /**
     * Digits only: `(011) 91234-5678` becomes `011912345678`. It strips and never interprets —
     * a `+55` stays, because guessing a country code is how a wrong number reaches a link.
     */
    public static function digits(mixed $phone): string
    {
        return is_string($phone) || is_int($phone)
            ? (string) preg_replace('/\D+/', '', (string) $phone)
            : '';
    }
}
