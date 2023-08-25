<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('VAT')->unique(); //ABC123
            $table->integer('category')->default(2);
            $table->string("manufacturer");
            $table->string("model")->nullable();
            $table->integer("rlYear")->nullable(); //pagaminimo metai
            $table->integer("driver_id")->nullable(); //pagaminimo metai
            $table->integer('status')->default(0); //0-laisva; 1-vaziuoja; 2-stovi parke; etc.
            $table->float('coordsx')->nullable(); //lokacija
            $table->float('corrdsy')->nullable(); //lokacija
            $table->string("VIN")->nullable();
            $table->string("tchExpirationDate")->nullable(); //technines apziuros galiojimo pabaigos data
            $table->unsignedinteger("user_id")->nullable();
            $table->date('deleted_at')->nullable();
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
        Schema::dropIfExists('transports');
    }
}
