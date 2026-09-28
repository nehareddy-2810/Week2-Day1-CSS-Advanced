<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class CiaController extends Controller
{
    public function index(): View
    {
        return view('cia-career.skill-gap');
    }

    public function skillGap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_role' => ['required', 'string', 'max:100'],
        ]);

        $userId = (int) $request->user()->id;
        $profile = $this->buildSkillProfile($userId);
        $analysis = $this->analyzeWithGroq($validated['target_role'], $profile);

        return response()->json([
            ...$analysis,
            'skill_profile' => $profile,
        ]);
    }

    private function buildSkillProfile(int $userId): array
    {
        $completedLessons = \DB::table('candidate_lessons')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->select(['skill', 'lesson_title'])
            ->get();

        $quizScores = \DB::table('candidate_quiz_scores')
            ->where('user_id', $userId)
            ->whereNotNull('taken_at')
            ->select(['skill', 'score', 'max_score'])
            ->get();

        $skills = [];
        foreach ($completedLessons as $lesson) {
            $skill = trim((string) $lesson->skill);
            if ($skill === '') {
                continue;
            }

            $skills[$skill] ??= ['skill' => $skill, 'lessons_completed' => 0, 'quiz_scores' => []];
            $skills[$skill]['lessons_completed']++;
        }

        foreach ($quizScores as $quiz) {
            $skill = trim((string) $quiz->skill);
            $maxScore = (float) $quiz->max_score;
            $score = (float) $quiz->score;
            if ($skill === '' || $maxScore <= 0 || $score < 0 || $score > $maxScore) {
                continue;
            }

            $skills[$skill] ??= ['skill' => $skill, 'lessons_completed' => 0, 'quiz_scores' => []];
            $skills[$skill]['quiz_scores'][] = ($score / $maxScore) * 100;
        }

        $profile = [];
        foreach ($skills as $skill) {
            $scores = $skill['quiz_scores'];
            $profile[] = [
                'skill' => $skill['skill'],
                'score' => $scores ? round(array_sum($scores) / count($scores)) : 0,
                'lessons_completed' => $skill['lessons_completed'],
                'quizzes_completed' => count($scores),
            ];
        }

        return $profile;
    }

    private function analyzeWithGroq(string $targetRole, array $profile): array
    {
        $apiKey = config('services.groq.api_key');
        if (!$apiKey) {
            return $this->fallbackAnalysis($profile);
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Analyze only the supplied candidate skill profile against the target role. Return JSON only with gaps (array of objects with skill, current, required), score (integer 0-100), and priority_learning (array of short strings). Use current values from the profile; do not invent candidate evidence. Return an empty gaps array when there is no profile data.',
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'target_role' => $targetRole,
                                'skill_profile' => $profile,
                            ], JSON_THROW_ON_ERROR),
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                return $this->fallbackAnalysis($profile);
            }

            $content = $response->json('choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            return $this->normalizeAnalysis($decoded) ?? $this->fallbackAnalysis($profile);
        } catch (\Throwable) {
            return $this->fallbackAnalysis($profile);
        }
    }

    private function normalizeAnalysis(mixed $analysis): ?array
    {
        if (!is_array($analysis)
            || !is_array($analysis['gaps'] ?? null)
            || !is_numeric($analysis['score'] ?? null)
            || !is_array($analysis['priority_learning'] ?? null)) {
            return null;
        }

        $gaps = [];
        foreach ($analysis['gaps'] as $gap) {
            if (!is_array($gap)
                || !is_string($gap['skill'] ?? null)
                || !is_numeric($gap['current'] ?? null)
                || !is_numeric($gap['required'] ?? null)) {
                return null;
            }

            $gaps[] = [
                'skill' => mb_substr(trim($gap['skill']), 0, 100),
                'current' => min(100, max(0, (float) $gap['current'])),
                'required' => min(100, max(0, (float) $gap['required'])),
            ];
        }

        $priorities = array_values(array_filter(
            $analysis['priority_learning'],
            fn ($item) => is_string($item) && trim($item) !== ''
        ));

        return [
            'gaps' => $gaps,
            'score' => (int) min(100, max(0, round((float) $analysis['score']))),
            'priority_learning' => array_slice(array_map(fn ($item) => mb_substr(trim($item), 0, 240), $priorities), 0, 5),
        ];
    }

    private function fallbackAnalysis(array $profile): array
    {
        $gaps = [];
        $priorities = [];
        foreach ($profile as $skill) {
            $score = (int) $skill['score'];
            if ($score < 75) {
                $gaps[] = ['skill' => $skill['skill'], 'current' => $score, 'required' => 75];
                $priorities[] = 'Strengthen ' . $skill['skill'] . ' with focused practice and a follow-up quiz.';
            }
        }

        if (!$profile) {
            $priorities[] = 'Complete lessons and quizzes to establish a skill baseline.';
        }

        $score = $profile
            ? (int) round(array_sum(array_column($profile, 'score')) / count($profile))
            : 0;

        return [
            'gaps' => $gaps,
            'score' => $score,
            'priority_learning' => array_slice($priorities, 0, 5),
        ];
    }
}