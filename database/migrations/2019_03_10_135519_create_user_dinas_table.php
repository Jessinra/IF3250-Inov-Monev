<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dinas', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('dinas_id');
            $table->string('role');

            // Foreign keys
            $table->foreign('id')->references('id')->on('users');
            $table->foreign('dinas_id')->references('id')->on('dinas');

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
        Schema::dropIfExists('user_dinas');
    }
}
