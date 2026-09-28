<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CiaFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_cia_returns_local_fallback_when_ai_providers_fail(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'api.groq.com/*' => Http::response([], 500),
            'openrouter.ai/*' => Http::response([], 500),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/cia-career/skill-gap', [
                'target_role' => 'Full Stack Developer',
            ]);

        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'gaps',
                'score',
                'priority_learning',
                'skill_profile',
            ]);
    }
}
