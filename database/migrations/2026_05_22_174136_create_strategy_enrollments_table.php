<?php
// database/migrations/2024_01_01_000002_create_strategy_enrollments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('strategy_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('strategy_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->enum('status', ['active', 'completed', 'expired', 'upgraded'])->default('active');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->unsignedBigInteger('upgraded_to')->nullable();
            $table->unsignedBigInteger('upgraded_from')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['strategy_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('strategy_enrollments');
    }
};