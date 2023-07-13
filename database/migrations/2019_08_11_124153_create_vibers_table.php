<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVibersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vibers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('viberId')->unique();
            $table->string('name')->nullable();
            $table->string('VAT')->nullable();
            $table->boolean('inProgress')->default(false);
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
        Schema::dropIfExists('vibers');
    }
}
