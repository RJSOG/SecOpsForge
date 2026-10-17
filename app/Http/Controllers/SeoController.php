<?php

namespace App\Http\Controllers;

use App\Enum\TeamEnum;
use App\Support\Seo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * robots.txt, generated so the sitemap URL follows APP_URL.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /editor',
            'Disallow: /login',
            'Disallow: /api/',
            '',
            'Sitemap: ' . Seo::url('sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * sitemap.xml: the static pages, the three sections and every note.
     */
    public function sitemap(): Response
    {
        $notes = NotesController::getAllNotes();
        $lastUpdate = $notes ? date('c', $notes[0]['mtime']) : null;

        $urls = [['loc' => Seo::url(), 'lastmod' => $lastUpdate]];

        foreach (TeamEnum::getAllValues() as $team) {
            $teamNotes = array_values(array_filter($notes, fn($n) => $n['team'] === $team));
            $urls[] = [
                'loc' => Seo::url($team),
                'lastmod' => $teamNotes ? date('c', $teamNotes[0]['mtime']) : null,
            ];
        }

        $urls[] = ['loc' => Seo::url('whoami'), 'lastmod' => null];

        foreach ($notes as $note) {
            $urls[] = ['loc' => Seo::url($note['url']), 'lastmod' => date('c', $note['mtime'])];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($this->encodePath($url['loc']), ENT_XML1) . '</loc>'
                . ($url['lastmod'] ? '<lastmod>' . $url['lastmod'] . '</lastmod>' : '')
                . "</url>\n";
        }

        $xml .= "</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Percent-encode each path segment (note names may contain spaces or accents).
     */
    private function encodePath(string $url): string
    {
        $parts = parse_url($url);
        $path = implode('/', array_map('rawurlencode', explode('/', $parts['path'] ?? '/')));

        return $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '') . $path;
    }
}
