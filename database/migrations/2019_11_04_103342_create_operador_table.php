<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operador', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dni',8);
            $table->string('nom_operador',50);
            $table->string('ape_operador',50);
            $table->string('foto',15)->nullable();
            $table->enum('estado',['0','1'])->default('0'); //0: activo y 1: inactivo 
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
        Schema::dropIfExists('operador');
    }
}
