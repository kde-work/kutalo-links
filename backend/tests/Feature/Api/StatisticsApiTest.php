<?php

namespace Tests\Feature\Api;

use App\Infrastructure\Link\Eloquent\LinkClickModel;
use App\Infrastructure\Link\Eloquent\ShortLinkModel;
use App\Models\User;
use Tests\Concerns\AuthenticatesUsers;
use Tests\TestCase;

/**
 * Проверяет получение статистики переходов по короткой ссылке.
 */
class StatisticsApiTest extends TestCase
{
    use AuthenticatesUsers;

    public function test_user_can_fetch_link_statistics(): void
    {
        $user = $this->authenticateUser();

        $link = ShortLinkModel::query()->create([
            'slug' => 'stats-link',
            'destination_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);

        LinkClickModel::query()->create([
            'short_link_id' => $link->id,
            'clicked_at' => now(),
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'referer' => 'https://referer.test',
        ]);

        $response = $this->getJson('/api/links/' . $link->id . '/statistics');

        $response
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('last_7_days', 1)
            ->assertJsonCount(1, 'recent_clicks');
    }

    public function test_statistics_not_available_for_foreign_link(): void
    {
        $this->authenticateUser();
        $otherUser = User::factory()->create();

        $link = ShortLinkModel::query()->create([
            'slug' => 'foreign-stats',
            'destination_url' => 'https://example.com',
            'user_id' => $otherUser->id,
        ]);

        $this->getJson('/api/links/' . $link->id . '/statistics')->assertNotFound();
    }
}
