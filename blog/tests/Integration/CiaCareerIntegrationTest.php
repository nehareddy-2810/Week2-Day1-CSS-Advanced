<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;

class CiaCareerIntegrationTest extends TestCase
{
    public function test_all_cia_modules_work()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 1. CIA Career Dashboard
        $response = $this->get('/cia-career');
        $response->assertStatus(200);

        // 2. Placement Readiness
        $response = $this->post('/cia-career/readiness', [
            'project_completion' => 80,
            'assessment_scores' => 75,
            'cia_usage' => 80,
            'docs_score' => 75,
            'viva_scores' => 70,
            'code_review' => 80,
            'demo_clarity' => 75,
            'git_quality' => 80,
        ]);

        $this->assertNotEquals(500, $response->status());

        // 3. Career Intelligence Report
        $response = $this->post('/career-intelligence/generate');

        $this->assertNotEquals(500, $response->status());

        // 4. Internship Matching / ChromaDB
        $response = $this->get('/internship-matches');

        $this->assertNotEquals(500, $response->status());

        // 5. Dashboard
        $response = $this->get('/dashboard');

        $this->assertNotEquals(500, $response->status());

        // 6. Profile
        $response = $this->get('/profile');

        $this->assertNotEquals(500, $response->status());

        // 7. Login page
        auth()->logout();

        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}