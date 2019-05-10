<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company', 200);
            $table->string('company_link', 200)->nullable();
            $table->string('city', 200)->nullable();
            $table->string('offer_link', 200)->nullable();
            $table->mediumText('description');
            $table->boolean('is_remote', false);
            $table->dateTimeTz('date')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
