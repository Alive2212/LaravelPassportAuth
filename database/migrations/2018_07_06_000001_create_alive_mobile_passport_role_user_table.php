<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliveMobilePassportRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alive_mobile_passport_role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')
                ->index();
            $table->foreign('role_id')
                ->references('id')
                ->on('alive_mobile_passport_roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alive_mobile_passport_role_user', function (Blueprint $table) {
            $table->drop('alive_mobile_passport_role_user');
        });
    }
}
