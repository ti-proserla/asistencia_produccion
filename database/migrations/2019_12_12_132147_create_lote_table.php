<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lote', function (Blueprint $table) {
            $table->increments('id');
            $table->string('guia',50);
            $table->string('transportista',70);
            $table->integer('materia_id')->unsigned()->nullable();
            $table->integer('proveedor_id')->unsigned()->nullable();
            $table->timestamp('registro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lote');
    }
}
