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
        Schema::create('twin_games', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('status', 20)->default('lobby'); // lobby, discussion, voting, reveal, finished
            $table->unsignedInteger('current_round')->default(1);
            $table->unsignedInteger('timer_seconds')->default(300);
            $table->timestamp('timer_ends_at')->nullable();
            $table->string('tie_break_rule', 20)->default('kick_all'); // kick_all, random
            $table->json('eliminated_player_ids')->nullable();
            $table->json('last_reveal_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twin_games');
    }
};
