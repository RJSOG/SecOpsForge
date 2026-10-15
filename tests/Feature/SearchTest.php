<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchTest extends TestCase
{
    public function test_short_queries_return_nothing(): void
    {
        $this->getJson('/api/search?q=a')->assertOk()->assertExactJson(['results' => []]);
    }

    public function test_filename_matches_rank_above_content_matches(): void
    {
        $this->makeNote('redteam', 'Kerberoasting', '# Kerberoasting');
        $this->makeNote('blueteam', 'Logs', 'detect kerberoasting with 4769');

        $this->getJson('/api/search?q=kerberoast')
            ->assertOk()
            ->assertJsonCount(2, 'results')
            ->assertJsonPath('results.0.url', '/redteam/Kerberoasting')
            ->assertJsonPath('results.1.url', '/blueteam/Logs');
    }

    public function test_search_is_rate_limited(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/search?q=xx')->assertOk();
        }

        $this->getJson('/api/search?q=xx')->assertStatus(429);
    }
}
