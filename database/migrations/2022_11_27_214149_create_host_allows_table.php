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
        Schema::create('host_allows', function (Blueprint $table) {
            $table->id();

            // host_id
            $table->unsignedBigInteger('host_id')->index();

            // 授权给另一个 host
            $table->unsignedBigInteger('allow_host_id')->index();


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
        Schema::dropIfExists('host_allows');
    }
};
