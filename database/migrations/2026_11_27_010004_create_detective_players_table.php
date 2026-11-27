<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detective_players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('detective_room_id')->constrained('detective_rooms')->cascadeOnDelete();
            $table->foreignId('detective_team_id')->nullable()->constrained('detective_teams')->nullOnDelete();
            $table->string('name', 40);
            $table->string('player_token', 64)->unique();
            $table->boolean('is_ready')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detective_players');
    }
};
