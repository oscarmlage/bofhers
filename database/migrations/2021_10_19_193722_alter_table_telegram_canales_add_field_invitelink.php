<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTelegramCanalesAddFieldInvitelink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_canales', function (Blueprint $table) {
            $table->string('invitelink', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_canales', function (Blueprint $table) {
            $table->dropColumn(['invitelink']);
        });
    }
}
