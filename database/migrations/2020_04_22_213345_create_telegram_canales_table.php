<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelegramCanalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_canales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chat_id', 70)->nullable();
            $table->string('name', 120)->nullable();
            $table->mediumText('description')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_canales');
    }
}
