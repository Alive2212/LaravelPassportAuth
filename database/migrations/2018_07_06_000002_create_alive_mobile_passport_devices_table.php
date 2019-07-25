<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliveMobilePassportDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alive_mobile_passport_devices', function (Blueprint $table) {
            $table->bigIncrements('id');

            // author
            $table->unsignedBigInteger('author_id')
                ->nullable()
                ->index();
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // User id
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // IMEI unique code
            $table->text('imei');

            // app name
            $table->string('app_name',150);

            // app version
            $table->text('app_version');

            // platform
            $table->text('platform');

            // os
            $table->text('os');

            // push notification
            $table->text('push_token');

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
        Schema::dropIfExists('alive_mobile_passport_devices');
    }
}
