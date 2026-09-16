<?php

declare(strict_types = 1);

namespace Goognet\Ui\Support;

use Illuminate\View\ComponentAttributeBag;
use Stringable;

final class SafeUrl
{
    private const string SCHEME = '/^([a-z][a-z0-9+.\-]*):/i';

    private const string DATA_IMAGE = '#^data:image/(png|jpe?g|gif|webp|avif|svg\+xml)[;,]#i';

    /** Attributes a browser navigates to, submits to or loads as a document. */
    private const array LINK_ATTRIBUTES = ['href', 'action', 'formaction', 'xlink:href', 'cite', 'data', 'longdesc'];

    /** Attributes a browser loads as an image or a poster frame. */
    private const array MEDIA_ATTRIBUTES = ['src', 'poster'];

    /** A whole HTML document inside an attribute: no value of it is safe to pass through. */
    private const array DROPPED_ATTRIBUTES = ['srcdoc'];

    /**
     * For `<a href>`. Relative paths, fragments and queries always pass; an absolute URL passes
     * only with a scheme from `goognet-ui.security.link_schemes`.
     */
    public static function href(mixed $url): ?string
    {
        $normalized = self::normalize($url);

        if ($normalized === null) {
            return null;
        }

        $scheme = self::scheme($normalized);

        if ($scheme === null) {
            return $normalized;
        }

        return self::allows('link_schemes', $scheme) ? $normalized : null;
    }

    /**
     * For `<img src>` and `<source srcset>`. An image cannot run script, so an image data URL is
     * accepted here and nowhere else.
     */
    public static function media(mixed $url): ?string
    {
        $normalized = self::normalize($url);

        if ($normalized === null) {
            return null;
        }

        $scheme = self::scheme($normalized);

        if ($scheme === null) {
            return $normalized;
        }

        if ($scheme === 'data') {
            return preg_match(self::DATA_IMAGE, $normalized) === 1 ? $normalized : null;
        }

        return self::allows('media_schemes', $scheme) ? $normalized : null;
    }

    /**
     * For `<iframe src>`. Absolute https with a host, nothing else: a frame is a whole document,
     * and a relative or data source is how a page ends up framing something it never meant to.
     */
    public static function frame(mixed $url): ?string
    {
        $normalized = self::normalize($url);

        if ($normalized === null || self::scheme($normalized) !== 'https') {
            return null;
        }

        $host = parse_url($normalized, PHP_URL_HOST);

        /** `parse_url` answers false for a malformed URL, and `filled(false)` is true. */
        if (! is_string($host) || $host === '') {
            return null;
        }

        /** @var list<string> $hosts */
        $hosts = config('goognet-ui.security.frame_hosts', []);

        if ($hosts === []) {
            return $normalized;
        }

        return in_array(strtolower($host), array_map(strtolower(...), $hosts), true) ? $normalized : null;
    }

    /**
     * The attributes a caller passes through `$attributes`, filtered the same way as the props:
     * a component that guards its `href` prop is still open through `formaction` or `xlink:href`.
     * An attribute whose URL is refused is removed rather than blanked.
     */
    public static function attributes(ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        $filtered = [];

        foreach ($attributes->getAttributes() as $name => $value) {
            $key = strtolower((string) $name);

            if (in_array($key, self::DROPPED_ATTRIBUTES, true)) {
                continue;
            }

            if (in_array($key, self::LINK_ATTRIBUTES, true)) {
                $value = self::href($value);
            } elseif (in_array($key, self::MEDIA_ATTRIBUTES, true)) {
                $value = self::media($value);
            }

            if ($value !== null) {
                $filtered[$name] = $value;
            }
        }

        return new ComponentAttributeBag($filtered);
    }

    /**
     * What a browser does before it reads the scheme: C0 controls and spaces are trimmed from the
     * ends, and tabs and newlines are removed from anywhere. Skipping this is what lets
     * `java\tscript:` through a check that only compares text.
     */
    private static function normalize(mixed $url): ?string
    {
        if (! is_string($url) && ! $url instanceof Stringable) {
            return null;
        }

        $normalized = str_replace(["\t", "\n", "\r"], '', trim((string) $url, "\x00..\x20"));

        return $normalized === '' ? null : $normalized;
    }

    private static function scheme(string $url): ?string
    {
        return preg_match(self::SCHEME, $url, $matches) === 1 ? strtolower($matches[1]) : null;
    }

    private static function allows(string $list, string $scheme): bool
    {
        /** @var list<string> $schemes */
        $schemes = config('goognet-ui.security.' . $list, []);

        return in_array($scheme, array_map(strtolower(...), $schemes), true);
    }
}
