<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('user_trading_infos', 'learning_style')) {
            Schema::table('user_trading_infos', function (Blueprint $table) {
                $table->string('learning_style')->nullable()->after('asset_classes');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('user_trading_infos', 'learning_style')) {
            Schema::table('user_trading_infos', function (Blueprint $table) {
                $table->dropColumn('learning_style');
            });
        }
    }
};