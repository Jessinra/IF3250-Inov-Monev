<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('role_permissions', function (Blueprint $table) {
//            $table->increments('id');
//            $table->unsignedInteger('role_id');
//            $table->unsignedInteger('permission_id');
//            $table->timestamps();
//
//            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
//            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permissions');
    }
}
