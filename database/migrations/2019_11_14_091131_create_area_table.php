<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area', function (Blueprint $table) {
            $table->string('id',3)->primary();
            $table->string('nom_area',50);
            $table->enum('estado',['0','1'])->default('0'); //0: activo y 1: inactivo 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area');
    }
}
