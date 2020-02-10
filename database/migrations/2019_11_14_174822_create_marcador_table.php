<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarcadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marcador', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo_operador',8);
            $table->dateTime('ingreso')->nullable();	
            $table->dateTime('salida')->nullable();
            $table->string('fundo_id',8)->nullable();
            $table->integer('cuenta_id')->nullable();
            $table->integer('turno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marcador');
    }
}
