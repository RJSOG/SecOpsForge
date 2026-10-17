<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Builds the meta tags rendered server-side in app.blade.php, so crawlers and
 * link previews (which do not run the Vue app) get a real title, description,
 * canonical URL and Open Graph card for every page.
 */
class Seo
{
    public const SITE_NAME = 'SecOpsForge';

    public const DEFAULT_DESCRIPTION = 'Cybersecurity knowledge base by Eliott Donatien (RJSOG): red team, blue team and automation notes, built by operators, for operators.';

    public const AUTHOR = 'Eliott Donatien';

    /**
     * @param string|null $title Page title, without the site name.
     * @param string|null $description Plain text, trimmed to ~160 characters.
     * @param string $type Open Graph type: "website", "article" or "profile".
     * @param bool $index false adds "noindex" (login, editor).
     * @param array|null $jsonLd schema.org object rendered as JSON-LD.
     */
    public static function make(
        ?string $title = null,
        ?string $description = null,
        string $type = 'website',
        bool $index = true,
        ?array $jsonLd = null,
    ): array {
        $url = self::url(request()->path());

        return [
            'title' => $title ? $title . ' | ' . self::SITE_NAME : self::SITE_NAME . ' · Cybersecurity knowledge base',
            'description' => Str::limit(self::clean($description ?? self::DEFAULT_DESCRIPTION), 160),
            'url' => $url,
            'type' => $type,
            'image' => self::url('og-image.png'),
            'robots' => $index ? 'index, follow' : 'noindex, nofollow',
            'jsonLd' => $jsonLd,
        ];
    }

    /**
     * Absolute URL on the public domain (APP_URL), whatever host the request
     * came through.
     */
    public static function url(string $path = ''): string
    {
        $path = trim($path, '/');

        return rtrim(config('app.url'), '/') . ($path === '' ? '/' : '/' . $path);
    }

    /**
     * Description of a rendered note: its first paragraph, skipping quotes and
     * code blocks.
     */
    public static function excerpt(string $html): ?string
    {
        $html = preg_replace('#<(blockquote|pre|table)\b.*?</\1>#is', '', $html);

        if (preg_match('#<p\b[^>]*>(.*?)</p>#is', $html, $m)) {
            $text = self::clean($m[1]);

            return $text !== '' ? $text : null;
        }

        return null;
    }

    private static function clean(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5)));
    }
}
