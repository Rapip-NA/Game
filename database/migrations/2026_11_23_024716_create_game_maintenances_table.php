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
        Schema::create('game_maintenances', function (Blueprint $table): void {
            $table->id();
            $table->string('game_key', 50)->unique();
            $table->string('game_name', 100);
            $table->boolean('is_maintenance')->default(false);
            $table->string('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_maintenances');
    }
};
