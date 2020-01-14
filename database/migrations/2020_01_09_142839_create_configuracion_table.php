<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfiguracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tiempo_entre_marcas')->default(5);
            $table->integer('hora_cierre_turno')->default(7);
            $table->string('actividad',100)->nullable();
            $table->string('ccosto',100)->nullable();
            // $table->timestamps();
        });
        DB::table('configuracion')->insert([
            ['tiempo_entre_marcas'=> 45,'hora_cierre_turno'=> 7,'actividad'=> '','ccosto'=> '']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion');
    }
}
