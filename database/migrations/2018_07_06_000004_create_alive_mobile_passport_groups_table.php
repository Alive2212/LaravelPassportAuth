<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliveMobilePassportGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alive_mobile_passport_groups', function (Blueprint $table) {
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

            // title
            $table->text('title');

            // subtitle
            $table->text('subtitle');

            // description
            $table->text('description');

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
        Schema::table('alive_mobile_passport_groups', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
            $table->drop('alive_mobile_passport_groups');
        });
    }
}
