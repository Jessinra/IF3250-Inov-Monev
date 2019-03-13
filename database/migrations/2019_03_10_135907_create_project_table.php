<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('name', 150);
            $table->unsignedInteger('dinas_id');
            $table->text('short_description');
            $table->longText('description');
            $table->string('status', 50);
            $table->dateTime('due_on');

            // Foreign keys
            $table->foreign('dinas_id')->references('id')->on('dinas');

            $table->timestamp('added_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
