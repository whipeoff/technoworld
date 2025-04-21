<?php
// core/Controllers/SitemapController.php

namespace Core\Controllers;

use Core\Models\Good;
use function slugify;
use DOMDocument;

/**
 * Админ‑генерация sitemap.xml
 * Доступна только роли admin: /admin/sitemap/generate
 */
class SitemapController extends BaseController
{
    public function generate(): void
    {
        $payload = \Core\Security\AuthManager::requireRole('admin', $this->pdo);
        $this->rateLimiter->check('sitemap_generate', 5);

        WTL("SitemapController: начал generate() от {$payload['username']}");
        $ok = $this->createXml();
        WTL("SitemapController: createXml() => " . ($ok ? 'OK' : 'FAIL'));

        $this->render('admin/sitemapResult.php', [
            'title'           => 'Генерация sitemap',
            'metaDescription' => 'Результат генерации sitemap.xml',
            'metaRobots'      => 'noindex, nofollow',
            'canonicalUrl'    => 'https://techno-world.free.nf/admin/sitemap/generate',
            'success'         => $ok,
        ], 'base');
    }

    private function createXml(): bool
    {
        $base = 'https://techno-world.free.nf';

        $routes = [
            ['loc' => '/',                'lastmod' => date('Y-m-d')],
            ['loc' => '/about',           'lastmod' => date('Y-m-d')],
            ['loc' => '/how-to-buy',      'lastmod' => date('Y-m-d')],
            ['loc' => '/catalog',         'lastmod' => date('Y-m-d')],
            ['loc' => '/request',         'lastmod' => date('Y-m-d')],
            ['loc' => '/request/success', 'lastmod' => date('Y-m-d')],
        ];

        $stmt = $this->pdo->query("SELECT id, type, brand, add_date FROM goods");
        while ($g = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $slug = slugify("{$g['type']} {$g['brand']}");
            $routes[] = [
                'loc'     => "/catalog/{$slug}-{$g['id']}",
                'lastmod' => date('Y-m-d', strtotime($g['add_date'])),
            ];
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $urlset = $doc->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $doc->appendChild($urlset);

        foreach ($routes as $r) {
            $url = $doc->createElement('url');

            $loc = $doc->createElement('loc', $base . $r['loc']);
            $lastmod = $doc->createElement('lastmod', $r['lastmod']);
            $changefreq = $doc->createElement('changefreq', 'weekly');
            $priority = $doc->createElement('priority', '0.7');

            $url->appendChild($loc);
            $url->appendChild($lastmod);
            $url->appendChild($changefreq);
            $url->appendChild($priority);

            $urlset->appendChild($url);
        }

        $file = __DIR__ . '/../../sitemap.xml';
        return $doc->save($file) !== false;
    }
}
