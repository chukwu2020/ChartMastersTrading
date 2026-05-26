<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('strategies', function (Blueprint $table) {
            // Change columns to JSON type if they aren't already
            $table->json('features')->nullable()->change();
            $table->json('modules')->nullable()->change();
            $table->json('learning_objectives')->nullable()->change();
            $table->json('prerequisites')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('strategies', function (Blueprint $table) {
            $table->text('features')->nullable()->change();
            $table->text('modules')->nullable()->change();
            $table->text('learning_objectives')->nullable()->change();
            $table->text('prerequisites')->nullable()->change();
        });
    }
};