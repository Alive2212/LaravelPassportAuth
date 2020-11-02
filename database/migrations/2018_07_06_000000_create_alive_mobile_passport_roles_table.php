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
            $table->bigIncrements('id');

            // Author
            $table->unsignedBigInteger('author_id')
                ->nullable()
                ->index();
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Key
            $table->string('key');

            // Content
            $table->string('title');
            $table->string('subtitle')
                ->nullable();
            $table->text('description')
                ->nullable();

            // Level
            $table->unsignedInteger('level')
                ->default(0);

            // IS OTP for send sms
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
        Schema::table('alive_mobile_passport_roles', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
            $table->drop('alive_mobile_passport_roles');
        });
    }
}
