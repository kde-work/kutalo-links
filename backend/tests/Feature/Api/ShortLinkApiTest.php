<?php

namespace Tests\Feature\Api;

use App\Infrastructure\Link\Eloquent\ShortLinkModel;
use App\Models\User;
use Tests\Concerns\AuthenticatesUsers;
use Tests\TestCase;

/**
 * Проверяет CRUD коротких ссылок и изоляцию данных между пользователями.
 */
class ShortLinkApiTest extends TestCase
{
    use AuthenticatesUsers;

    public function test_links_endpoints_require_authentication(): void
    {
        $this->getJson('/api/links')->assertUnauthorized();
        $this->postJson('/api/links', [
            'destination_url' => 'https://example.com',
        ])->assertUnauthorized();
    }

    public function test_user_can_create_link_with_custom_slug(): void
    {
        $this->authenticateUser();

        $response = $this->postJson('/api/links', [
            'slug' => 'promo2024',
            'destination_url' => 'https://example.com/landing',
            'title' => 'Промо',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('slug', 'promo2024')
            ->assertJsonPath('destination_url', 'https://example.com/landing')
            ->assertJsonPath('title', 'Промо')
            ->assertJsonPath('is_active', true)
            ->assertJsonPath('clicks_count', 0);

        $this->assertDatabaseHas('short_links', [
            'slug' => 'promo2024',
            'destination_url' => 'https://example.com/landing',
        ]);
    }

    public function test_user_can_list_own_links(): void
    {
        $user = $this->authenticateUser();
        $otherUser = User::factory()->create();

        ShortLinkModel::query()->create([
            'slug' => 'mine',
            'destination_url' => 'https://example.com/mine',
            'user_id' => $user->id,
        ]);
        ShortLinkModel::query()->create([
            'slug' => 'other',
            'destination_url' => 'https://example.com/other',
            'user_id' => $otherUser->id,
        ]);

        $response = $this->getJson('/api/links');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.slug', 'mine');
    }

    public function test_user_cannot_access_another_users_link(): void
    {
        $this->authenticateUser();
        $otherUser = User::factory()->create();

        $link = ShortLinkModel::query()->create([
            'slug' => 'foreign',
            'destination_url' => 'https://example.com/foreign',
            'user_id' => $otherUser->id,
        ]);

        $this->getJson('/api/links/' . $link->id)->assertNotFound();
        $this->patchJson('/api/links/' . $link->id, ['title' => 'Взлом'])->assertNotFound();
        $this->deleteJson('/api/links/' . $link->id)->assertNotFound();
    }

    public function test_user_can_update_and_deactivate_link(): void
    {
        $user = $this->authenticateUser();

        $link = ShortLinkModel::query()->create([
            'slug' => 'update-me',
            'destination_url' => 'https://example.com/old',
            'title' => 'Старое',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $this->patchJson('/api/links/' . $link->id, [
            'title' => 'Новое',
            'is_active' => false,
        ])
            ->assertOk()
            ->assertJsonPath('title', 'Новое')
            ->assertJsonPath('is_active', false);
    }

    public function test_user_can_update_destination(): void
    {
        $user = $this->authenticateUser();

        $link = ShortLinkModel::query()->create([
            'slug' => 'dest-update',
            'destination_url' => 'https://example.com/old',
            'user_id' => $user->id,
        ]);

        $this->patchJson('/api/links/' . $link->id . '/destination', [
            'destination_url' => 'https://example.com/new',
        ])
            ->assertOk()
            ->assertJsonPath('destination_url', 'https://example.com/new');
    }

    public function test_user_can_delete_link(): void
    {
        $user = $this->authenticateUser();

        $link = ShortLinkModel::query()->create([
            'slug' => 'delete-me',
            'destination_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);

        $this->deleteJson('/api/links/' . $link->id)->assertNoContent();

        $this->assertDatabaseMissing('short_links', ['id' => $link->id]);
    }

    public function test_create_rejects_duplicate_slug(): void
    {
        $user = $this->authenticateUser();

        ShortLinkModel::query()->create([
            'slug' => 'taken',
            'destination_url' => 'https://example.com/existing',
            'user_id' => $user->id,
        ]);

        $this->postJson('/api/links', [
            'slug' => 'taken',
            'destination_url' => 'https://example.com/new',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Slug уже занят: taken');
    }

    public function test_create_rejects_reserved_slug(): void
    {
        $this->authenticateUser();

        $this->postJson('/api/links', [
            'slug' => 'api',
            'destination_url' => 'https://example.com',
        ])
            ->assertUnprocessable();
    }
}
