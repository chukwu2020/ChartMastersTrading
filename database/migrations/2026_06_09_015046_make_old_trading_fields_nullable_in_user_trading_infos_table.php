<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('user_trading_infos', function (Blueprint $table) {
        $table->string('annual_income')->nullable()->change();
        $table->string('financial_alternative')->nullable()->change();
        $table->string('ongoing_deposit_source')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_trading_infos', function (Blueprint $table) {
            //
        });
    }
};
