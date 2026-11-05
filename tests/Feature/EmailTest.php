<?php

declare(strict_types = 1);

use Goognet\Ui\Support\Email;

it('collects distinct company email addresses in display order', function (array $company, array $expected): void {
    config(['goognet-ui.company' => $company]);

    expect(Email::companyAddresses())->toBe($expected);
})->with([
    'legacy configuration'      => [['mail' => 'hello@example.com'], ['hello@example.com']],
    'list without legacy email' => [
        ['mails' => ['sales@example.com', 'support@example.com', 'billing@example.com']],
        ['sales@example.com', 'support@example.com', 'billing@example.com'],
    ],
    'legacy first with whitespace and case duplicates removed' => [
        ['mail' => ' Hello@Example.com ', 'mails' => ['hello@example.com', ' sales@example.com ', 'SALES@example.com']],
        ['Hello@Example.com', 'sales@example.com'],
    ],
    'empty invalid and unsupported values' => [
        ['mail' => null, 'mails' => [null, '', ' ', false, [], 123, 'invalid', '/var/spool/mail/site', 'hello@example.com?bcc=other@example.com', "hello@example.com\r\nBcc: other@example.com"]],
        [],
    ],
    'invalid primary with valid additional email' => [
        ['mail' => 'invalid', 'mails' => ['sales@example.com']],
        ['sales@example.com'],
    ],
    'distinct plus addresses' => [
        ['mail' => 'hello@example.com', 'mails' => ['hello+sales@example.com']],
        ['hello@example.com', 'hello+sales@example.com'],
    ],
    'no configuration' => [[], []],
]);

it('encodes mailbox characters without introducing mailto headers or fragments', function (string $email, string $expected): void {
    expect(Email::url($email))->toBe($expected);
})->with([
    'ordinary address'    => ['hello@example.com', 'mailto:hello@example.com'],
    'plus address'        => ['hello+sales@example.com', 'mailto:hello%2Bsales@example.com'],
    'reserved characters' => ['sales?&=#%/team@example.com', 'mailto:sales%3F%26%3D%23%25%2Fteam@example.com'],
]);
