<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

final class Email
{
    /**
     * @return list<string>
     */
    public static function companyAddresses(): array
    {
        return collect([
            config('goognet-ui.company.mail'),
            ...(array) config('goognet-ui.company.mails', []),
        ])
            ->filter(fn (mixed $email): bool => is_string($email))
            ->map(fn (string $email): string => trim($email))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique(fn (string $email): string => strtolower($email))
            ->values()
            ->all();
    }

    public static function url(string $email): string
    {
        return 'mailto:' . str_replace('%40', '@', rawurlencode($email));
    }
}
