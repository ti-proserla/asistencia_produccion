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
            $table->integer('turno_id')->unsigned();
            $table->integer('operador_id')->unsigned();
            $table->integer('proceso_id')->unsigned();
            $table->string('labor_id',6);
            $table->string('area_id',3);
            $table->integer('linea_id')->unsigned()->nullable();
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
        Schema::dropIfExists('tareo');
    }
}
