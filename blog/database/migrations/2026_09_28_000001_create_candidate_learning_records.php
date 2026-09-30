<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('lesson_title');
            $table->string('skill');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'completed_at']);
        });

        Schema::create('candidate_quiz_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('quiz_title');
            $table->string('skill');
            $table->decimal('score', 7, 2);
            $table->decimal('max_score', 7, 2);
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'taken_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_quiz_scores');
        Schema::dropIfExists('candidate_lessons');
    }
};