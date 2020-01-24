<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('turno_id')->unsigned()->nullable();
            $table->string('codigo_operador',8);
            $table->string('proceso_id',8);
            $table->string('labor_id',6);
            $table->string('area_id',3);
            $table->integer('linea_id')->unsigned()->nullable();
            $table->string('fundo_id',8)->nullable();
            $table->date('fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tareo');
    }
}
