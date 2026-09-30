<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CiaSkillGapTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_receives_a_skill_gap_analysis_from_only_their_completed_records(): void
    {
        config(['services.groq.api_key' => null]);
        $candidate = User::factory()->create();
        $otherCandidate = User::factory()->create();

        $this->insertLesson($candidate, 'PHP', 'Routing basics', true);
        $this->insertLesson($candidate, 'PHP', 'Middleware', false);
        $this->insertLesson($otherCandidate, 'React', 'Hooks', true);
        $this->insertQuiz($candidate, 'PHP', 6, 10);
        $this->insertQuiz($candidate, 'PHP', 8, 10);
        $this->insertQuiz($otherCandidate, 'React', 10, 10);

        $response = $this->actingAs($candidate)
            ->postJson('/cia-career/skill-gap', ['target_role' => 'Backend Developer']);

        $response->assertOk()
            ->assertJsonPath('score', 70)
            ->assertJsonPath('skill_profile.0.skill', 'PHP')
            ->assertJsonPath('skill_profile.0.score', 70)
            ->assertJsonPath('skill_profile.0.lessons_completed', 1)
            ->assertJsonPath('skill_profile.0.quizzes_completed', 2)
            ->assertJsonPath('gaps.0.skill', 'PHP')
            ->assertJsonStructure(['gaps', 'score', 'priority_learning', 'skill_profile']);
    }

    public function test_skill_gap_endpoint_requires_authentication_and_a_target_role(): void
    {
        $this->postJson('/cia-career/skill-gap', ['target_role' => 'Backend Developer'])
            ->assertUnauthorized();

        $this->actingAs(User::factory()->create())
            ->postJson('/cia-career/skill-gap', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('target_role');
    }

    public function test_groq_analysis_is_normalized_to_the_json_contract(): void
    {
        config(['services.groq.api_key' => 'test-key']);
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'gaps' => [['skill' => 'PHP', 'current' => 62, 'required' => 80]],
                        'score' => 72.4,
                        'priority_learning' => ['Practice API design'],
                    ])],
                ]],
            ]),
        ]);

        $candidate = User::factory()->create();
        $this->insertLesson($candidate, 'PHP', 'Routing basics', true);
        $this->insertQuiz($candidate, 'PHP', 6, 10);

        $this->actingAs($candidate)
            ->postJson('/cia-career/skill-gap', ['target_role' => 'Backend Developer'])
            ->assertOk()
            ->assertJsonPath('score', 72)
            ->assertJsonPath('gaps.0.current', 62)
            ->assertJsonPath('gaps.0.required', 80)
            ->assertJsonPath('priority_learning.0', 'Practice API design');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.groq.com')
            && $request['model'] === 'llama-3.3-70b-versatile');
    }

    public function test_malformed_groq_response_falls_back_to_local_analysis(): void
    {
        config(['services.groq.api_key' => 'test-key']);
        Http::fake(['api.groq.com/*' => Http::response(['choices' => []])]);
        $candidate = User::factory()->create();
        $this->insertLesson($candidate, 'PHP', 'Routing basics', true);
        $this->insertQuiz($candidate, 'PHP', 4, 10);

        $this->actingAs($candidate)
            ->postJson('/cia-career/skill-gap', ['target_role' => 'Backend Developer'])
            ->assertOk()
            ->assertJsonPath('score', 40)
            ->assertJsonPath('gaps.0.skill', 'PHP')
            ->assertJsonPath('gaps.0.current', 40)
            ->assertJsonPath('gaps.0.required', 75);
    }

    private function insertLesson(User $user, string $skill, string $title, bool $completed): void
    {
        \DB::table('candidate_lessons')->insert([
            'user_id' => $user->id,
            'lesson_title' => $title,
            'skill' => $skill,
            'completed_at' => $completed ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertQuiz(User $user, string $skill, int $score, int $maxScore): void
    {
        \DB::table('candidate_quiz_scores')->insert([
            'user_id' => $user->id,
            'quiz_title' => $skill . ' checkpoint',
            'skill' => $skill,
            'score' => $score,
            'max_score' => $maxScore,
            'taken_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
