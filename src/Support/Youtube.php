<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

/**
 * Reads a YouTube address with string work only. It never asks YouTube anything: checking which
 * thumbnail exists at render time cost up to four blocking requests per video.
 */
final class Youtube
{
    /** Eleven characters of the URL-safe alphabet; the length keeps `/watch` from passing for an id. */
    private const string ID = '[A-Za-z0-9_-]{11}';

    /** @var list<string> */
    private const array PATTERNS = [
        '#[?&]v=(' . self::ID . ')#',
        '#youtu\.be/(' . self::ID . ')#',
        '#/embed/(' . self::ID . ')#',
        '#/shorts/(' . self::ID . ')#',
        '#/live/(' . self::ID . ')#',
        '#/v/(' . self::ID . ')#',
    ];

    /** @var array<string, string> */
    private const array THUMBNAILS = [
        'max'      => 'maxresdefault.jpg',
        'standard' => 'sddefault.jpg',
        'high'     => 'hqdefault.jpg',
        'medium'   => 'mqdefault.jpg',
    ];

    /**
     * Only the id is kept. Every URL the component emits is rebuilt from it, so whatever host
     * the input named, the link can only ever point at YouTube.
     */
    public static function id(mixed $url): ?string
    {
        $url = is_string($url) ? trim($url) : '';

        if ($url === '') {
            return null;
        }

        if (preg_match('#^' . self::ID . '$#', $url) === 1) {
            return $url;
        }

        foreach (self::PATTERNS as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function thumbnail(string $id, string $quality = 'max'): string
    {
        return 'https://i.ytimg.com/vi/' . $id . '/' . (self::THUMBNAILS[$quality] ?? self::THUMBNAILS['max']);
    }

    public static function watchUrl(string $id): string
    {
        return 'https://www.youtube.com/watch?v=' . $id;
    }
}
