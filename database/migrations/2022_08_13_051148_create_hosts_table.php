<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();

            // name
            $table->string('name')->index();

            // user_id
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');


            // host_id
            $table->unsignedBigInteger('host_id')->index();

            // price
            $table->double('price', 60, 8)->index();

            $table->double('managed_price', 60, 8)->index()->nullable();

            // config
            $table->json('configuration')->nullable();

            // status
            $table->string('status')->default('pending')->index();

            $table->timestamp('suspended_at')->nullable()->index();

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
        Schema::dropIfExists('hosts');
    }
};
