<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('quote')->nullable();
            $table->boolean('active')->default(false);
            $table->string('nick', 70)->nullable();
            $table->string('first_name', 70)->nullable();
            $table->string('last_name', 70)->nullable();
            $table->string('telegram_user_id', 70)->nullable();
            $table->string('chat_id', 70)->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('quotes');
    }
}
