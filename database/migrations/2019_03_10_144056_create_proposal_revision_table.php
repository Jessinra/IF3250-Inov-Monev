<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalRevisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal_revision', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('user_id');
            $table->longText('revision');

            // Foreign keys
            $table->foreign('project_id')->references('id')->on('projects');
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
        Schema::dropIfExists('proposal_revision');
    }
}
