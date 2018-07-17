<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliveMobilePassportRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alive_mobile_passport_roles', function (Blueprint $table) {
            $table->increments('id');

            // author
            $table->unsignedInteger('author_id')
                ->nullable()
                ->index();
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // title
            $table->text('title');

            // subtitle
            $table->text('subtitle');

            // description
            $table->text('description');

            // level
            $table->unsignedInteger('level')
                ->default(0);

            // revoked
            $table->boolean('is_otp')
                ->default(false);

            // revoked
            $table->boolean('revoked')
                ->default(false);

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
        Schema::dropIfExists('alive_mobile_passport_roles');
    }
}
