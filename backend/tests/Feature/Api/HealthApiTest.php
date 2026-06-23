<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

/**
 * Проверяет публичный health endpoint API.
 */
class HealthApiTest extends TestCase
{
    public function test_health_returns_ok_status(): void
    {
        $response = $this->getJson('/api/health');

        $response
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'service' => 'kutalo-links',
            ]);
    }
}
