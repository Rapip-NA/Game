<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('twin_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('twin_game_id')->constrained('twin_games')->cascadeOnDelete();
            $table->unsignedInteger('round_number')->default(1);
            $table->foreignId('voter_player_id')->constrained('twin_players')->cascadeOnDelete();
            $table->foreignId('voted_player_id')->constrained('twin_players')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['twin_game_id', 'round_number', 'voter_player_id', 'voted_player_id'], 'vote_round_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twin_votes');
    }
};
