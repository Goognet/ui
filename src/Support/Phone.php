<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Phone
{
    /**
     * The legacy phone stays first; additional numbers share one order and deduplication
     * rule across the footer, the site's contact links and its structured data.
     *
     * @return list<string>
     */
    public static function companyNumbers(): array
    {
        return collect([
            config('goognet-ui.company.phone'),
            ...(array) config('goognet-ui.company.phones', []),
        ])
            ->filter(fn (mixed $phone): bool => is_string($phone) || is_int($phone))
            ->map(fn (mixed $phone): string => trim((string) $phone))
            ->filter(fn (string $phone): bool => self::digits($phone) !== '')
            ->unique(self::digits(...))
            ->values()
            ->all();
    }

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
