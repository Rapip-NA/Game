<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detective_rooms', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 6)->unique();
            $table->string('host_token', 64);
            $table->foreignId('detective_case_id')->nullable()->constrained('detective_cases')->nullOnDelete();
            $table->string('phase', 30)->default('lobby');
            $table->string('investigation_mode', 20)->default('private');
            $table->unsignedInteger('target_players')->default(17);
            $table->unsignedInteger('default_lives')->default(10);
            $table->json('phase_durations');
            $table->timestamp('phase_ends_at')->nullable();
            $table->unsignedInteger('phase_paused_remaining')->nullable();
            $table->unsignedBigInteger('defense_team_id')->nullable();
            $table->json('breaking_news_feed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detective_rooms');
    }
};
