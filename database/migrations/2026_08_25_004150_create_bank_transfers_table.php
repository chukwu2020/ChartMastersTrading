<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('deposit_id')->nullable()->constrained()->onDelete('set null');
            $table->string('country');
            $table->decimal('amount', 15, 2);
            $table->string('request_code')->unique();
            $table->enum('status', ['pending', 'details_sent', 'completed', 'expired'])->default('pending');
            $table->timestamp('bank_details_sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->json('bank_details')->nullable()->after('notes');
            $table->timestamp('bank_details_sent_at')->nullable()->after('bank_details');
        });
    }

    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['bank_details', 'bank_details_sent_at']);
        });
        Schema::dropIfExists('bank_transfers');
    }
};