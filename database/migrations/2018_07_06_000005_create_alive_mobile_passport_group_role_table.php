<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliveMobilePassportGroupRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alive_mobile_passport_group_role', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')
                ->index();
            $table->foreign('role_id')
                ->references('id')
                ->on('alive_mobile_passport_roles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('group_id')
                ->index();
            $table->foreign('group_id')
                ->references('id')
                ->on('alive_mobile_passport_groups')
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
        Schema::dropIfExists('alive_mobile_passport_group_role');
    }
}