<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detective_cases', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->text('description');
            $table->string('category', 80)->default('System & Logic');
            $table->string('difficulty', 20)->default('medium');
            $table->text('initial_assumption');
            $table->json('mission_objectives');
            $table->json('hidden_facts');
            $table->json('evidence');
            $table->json('false_leads')->nullable();
            $table->json('special_hints')->nullable();
            $table->json('breaking_news')->nullable();
            $table->text('root_cause');
            $table->json('recommended_solutions');
            $table->text('validation_method');
            $table->unsignedInteger('default_duration')->default(20);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detective_cases');
    }
};
