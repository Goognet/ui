<?php

declare(strict_types = 1);

use Goognet\Ui\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

function componentSource(string $name): string
{
    return (string) file_get_contents(__DIR__ . '/../resources/views/components/' . $name . '.blade.php');
}
