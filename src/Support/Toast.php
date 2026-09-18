<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\Session\Store;

/**
 * What a toast says, taken from the call site or from what the last request flashed.
 *
 * A form that worked redirects with `->with('success', 'Mensagem enviada.')`, and the layout
 * carries `<x-ui.toast />` once: that is the whole path from the controller to the screen.
 */
final class Toast
{
    /** Flash keys read in order, each carrying the icon its name promises. */
    private const array KEYS = [
        'toast'   => null,
        'success' => 'success',
        'status'  => 'success',
        'error'   => 'error',
        'warning' => 'warning',
        'info'    => 'info',
    ];

    private const array ICONS = ['success', 'error', 'warning', 'info', 'question'];

    private const array POSITIONS = ['top', 'top-start', 'top-end', 'center', 'bottom', 'bottom-start', 'bottom-end'];

    /**
     * @return array{message: string, title?: string, icon?: string, position: string, duration: int}|null
     */
    public static function resolve(
        ?string $message = null,
        ?string $title = null,
        ?string $type = null,
        string $position = 'top-end',
        int $duration = 4000,
        ?string $session = null,
    ): ?array {
        $flashed = self::fromSession($session);

        $message ??= $flashed['message'] ?? null;

        if (blank($message)) {
            return null;
        }

        $type ??= $flashed['type'] ?? null;

        return array_filter([
            'message'  => $message,
            'title'    => $title ?? $flashed['title'] ?? null,
            'icon'     => in_array($type, self::ICONS, true) ? $type : null,
            'position' => in_array($position, self::POSITIONS, true) ? $position : 'top-end',

            /** Between a second and a minute: below that nobody reads it, above it never leaves. */
            'duration' => max(1000, min(60000, $duration)),
        ], fn (mixed $value): bool => $value !== null);
    }

    /**
     * @return array{message?: string, title?: string, type?: string}
     */
    private static function fromSession(?string $key): array
    {
        /** No session bound at all is the console, where there is nothing flashed to read. */
        if (! app()->bound('session.store')) {
            return [];
        }

        $session = app(Store::class);

        /** A named key is read on its own, with no icon assumed from a name nobody promised. */
        if (filled($key)) {
            return self::normalize($session->get($key), null);
        }

        foreach (self::KEYS as $name => $type) {
            if ($session->has($name)) {
                return self::normalize($session->get($name), $type);
            }
        }

        return [];
    }

    /**
     * A flash is a string in the common case and an array when it carries a title or an icon
     * of its own.
     *
     * @return array{message?: string, title?: string, type?: string}
     */
    private static function normalize(mixed $flash, ?string $type): array
    {
        if (is_string($flash) || is_numeric($flash)) {
            return array_filter(['message' => (string) $flash, 'type' => $type], filled(...));
        }

        if (! is_array($flash)) {
            return [];
        }

        return array_filter([
            'message' => is_scalar($flash['message'] ?? null) ? (string) $flash['message'] : null,
            'title'   => is_scalar($flash['title'] ?? null) ? (string) $flash['title'] : null,
            'type'    => is_string($flash['type'] ?? null) ? $flash['type'] : $type,
        ], filled(...));
    }
}
