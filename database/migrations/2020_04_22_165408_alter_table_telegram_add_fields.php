<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTelegramAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram', function (Blueprint $table) {
            $table->string('first_name', 70)->nullable();
            $table->string('last_name', 70)->nullable();
            $table->string('telegram_user_id', 70)->nullable();
            $table->mediumText('request')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'telegram_user_id', 'request'
            ]);
        });
    }
}
