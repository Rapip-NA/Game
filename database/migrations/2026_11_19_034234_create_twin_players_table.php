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
        Schema::create('twin_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('twin_game_id')->constrained('twin_games')->cascadeOnDelete();
            $table->string('username', 50);
            $table->string('player_token', 40)->unique();
            $table->string('secret_logo', 20)->nullable();
            $table->boolean('is_kicked')->default(false);
            $table->boolean('is_winner')->default(false);
            $table->unsignedInteger('kick_round')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twin_players');
    }
};
