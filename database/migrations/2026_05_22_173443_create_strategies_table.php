<?php
// database/migrations/2024_01_01_000001_create_strategies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('description');
            $table->longText('long_description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('duration_days')->nullable()->comment('Access duration in days, null = lifetime');
            $table->json('features')->nullable();
            $table->json('modules')->nullable();
            $table->string('difficulty_level')->nullable();
            $table->json('learning_objectives')->nullable();
            $table->json('prerequisites')->nullable();
            $table->string('instructor_name')->nullable();
            $table->text('instructor_bio')->nullable();
            $table->string('instructor_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('badge_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('estimated_hours')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('strategies');
    }
};