<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detective_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('detective_room_id')->constrained('detective_rooms')->cascadeOnDelete();
            $table->foreignId('detective_team_id')->constrained('detective_teams')->cascadeOnDelete();
            $table->foreignId('detective_player_id')->nullable()->constrained('detective_players')->nullOnDelete();
            $table->text('question_text');
            $table->boolean('is_expert_consultation')->default(false);
            $table->string('status', 30)->default('pending');
            $table->text('answer_text')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detective_questions');
    }
};
