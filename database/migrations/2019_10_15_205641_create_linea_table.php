<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linea', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre',10);// varchar
            $table->string('codigo',5)->nullable();
        });



        DB::table('linea')->insert([
            ["nombre"=>"LINEA 01"],
            ["nombre"=>"LINEA 02"],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linea');
    }
}
