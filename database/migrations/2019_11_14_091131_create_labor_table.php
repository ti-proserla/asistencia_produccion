<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaborTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labor', function (Blueprint $table) {
            $table->string('id',6)->primary();
            $table->string('nom_labor',80);
            $table->string('area_id',3)->nullable();
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
        Schema::dropIfExists('labor');
    }
}
