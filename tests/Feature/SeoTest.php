<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://secopsforge.example']);
    }

    public function test_home_has_meta_and_open_graph_tags(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical" href="https://secopsforge.example/">', false)
            ->assertSee('<meta property="og:image" content="https://secopsforge.example/og-image.png">', false)
            ->assertSee('"@type":"WebSite"', false);
    }

    public function test_note_page_uses_its_title_and_first_paragraph(): void
    {
        $this->makeNote('redteam', 'ad/Kerberoasting', "# Kerberoasting\n\n> Quote that should be skipped.\n\nRequest TGS tickets & crack them offline.\n\n```bash\nGetUserSPNs.py\n```");

        $this->get('/redteam/ad/Kerberoasting')
            ->assertOk()
            ->assertSee('<title inertia>Kerberoasting · Red Team | SecOpsForge</title>', false)
            ->assertSee('<meta name="description" content="Request TGS tickets &amp; crack them offline.">', false)
            ->assertSee('<link rel="canonical" href="https://secopsforge.example/redteam/ad/Kerberoasting">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('"@type":"TechArticle"', false);
    }

    public function test_note_title_cannot_break_out_of_json_ld(): void
    {
        $this->makeNote('redteam', '</script><script>alert(1)', '# x');

        $response = $this->get('/redteam/' . rawurlencode('</script><script>alert(1)'));

        $response->assertOk();
        $this->assertStringNotContainsString('</script><script>alert(1)', $response->getContent());
    }

    public function test_login_is_not_indexed(): void
    {
        $this->get('/login')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_robots_txt_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /editor')
            ->assertSee('Sitemap: https://secopsforge.example/sitemap.xml');
    }

    public function test_sitemap_lists_pages_and_notes(): void
    {
        $this->makeNote('blueteam', 'detection/Détecter un Kerberoasting', '# x');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>https://secopsforge.example/</loc>', false)
            ->assertSee('<loc>https://secopsforge.example/whoami</loc>', false)
            ->assertSee('<loc>https://secopsforge.example/blueteam/detection/D%C3%A9tecter%20un%20Kerberoasting</loc>', false)
            ->assertDontSee('/editor', false);
    }
}
