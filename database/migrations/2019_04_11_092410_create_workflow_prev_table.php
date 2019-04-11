<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowPrevTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_prev', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('current_id');
            $table->unsignedInteger('prev_id');
            $table->timestamps();

            $table->foreign('current_id')->references('id')->on('stages')->onDelete('cascade');
            $table->foreign('prev_id')->references('id')->on('stages')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_prev');
    }
}
