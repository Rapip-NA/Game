<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detective_teams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('detective_room_id')->constrained('detective_rooms')->cascadeOnDelete();
            $table->string('name', 60);
            $table->unsignedInteger('max_capacity')->default(4);
            $table->unsignedInteger('sort_order')->default(1);
            $table->unsignedInteger('lives')->default(10);
            $table->unsignedInteger('questions_asked_count')->default(0);
            $table->text('notes')->nullable();
            $table->text('hypothesis')->nullable();
            $table->json('eliminated_causes')->nullable();
            $table->json('evidence_items')->nullable();
            $table->json('final_report')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('scores')->nullable();
            $table->unsignedInteger('total_score')->default(0);
            $table->json('awards')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detective_teams');
    }
};
