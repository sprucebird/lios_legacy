<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthenticationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->Integer('code');
            $table->Integer('created_by_user');
            $table->boolean('used')->default(false);
            $table->string('purpose'); //signup, login, verify user, verify payment, authorize action
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
        Schema::dropIfExists('authentication_codes');
    }
}
