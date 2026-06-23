<?php

namespace Tests\Feature;

use App\Infrastructure\Link\Eloquent\LinkClickModel;
use App\Infrastructure\Link\Eloquent\ShortLinkModel;
use App\Models\User;
use Tests\TestCase;

/**
 * Проверяет публичный редирект по slug и запись кликов.
 */
class RedirectTest extends TestCase
{
    public function test_active_link_redirects_to_destination(): void
    {
        $user = User::factory()->create();

        ShortLinkModel::query()->create([
            'slug' => 'go-here',
            'destination_url' => 'https://example.com/target',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $response = $this->get('/go-here', [
            'HTTP_USER_AGENT' => 'Test Browser',
            'HTTP_REFERER' => 'https://referer.test',
        ]);

        $response
            ->assertRedirect('https://example.com/target')
            ->assertStatus(302);

        $this->assertDatabaseHas('link_clicks', [
            'ip' => '127.0.0.1',
            'user_agent' => 'Test Browser',
            'referer' => 'https://referer.test',
        ]);
    }

    public function test_inactive_link_returns_not_found(): void
    {
        $user = User::factory()->create();

        ShortLinkModel::query()->create([
            'slug' => 'paused',
            'destination_url' => 'https://example.com',
            'is_active' => false,
            'user_id' => $user->id,
        ]);

        $this->get('/paused')->assertNotFound();
        $this->assertDatabaseCount('link_clicks', 0);
    }

    public function test_unknown_slug_returns_not_found(): void
    {
        $this->get('/missing-slug')->assertNotFound();
    }

    public function test_redirect_increments_clicks_count(): void
    {
        $user = User::factory()->create();

        $link = ShortLinkModel::query()->create([
            'slug' => 'count-me',
            'destination_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);

        $this->get('/count-me')->assertRedirect();

        $this->assertSame(1, LinkClickModel::query()->where('short_link_id', $link->id)->count());
    }
}
