<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_modulo',100);
            $table->timestamps();
        });

        DB::table('modulo')->insert([
            ['id'=> 1,'nombre_modulo'=>'marcador'],
            ['id'=> 2,'nombre_modulo'=>'tareo'],
            ['id'=> 3,'nombre_modulo'=>'regularizar'],
            ['id'=> 4,'nombre_modulo'=>'operador'],
            ['id'=> 5,'nombre_modulo'=>'fotocheck-frame'],
            ['id'=> 6,'nombre_modulo'=>'cuenta'],
            ['id'=> 7,'nombre_modulo'=>'planilla'],
            ['id'=> 8,'nombre_modulo'=>'cargo'],
            ['id'=> 9,'nombre_modulo'=>'linea'],
            ['id'=> 10,'nombre_modulo'=>'proceso'],
            ['id'=> 11,'nombre_modulo'=>'area'],
            ['id'=> 12,'nombre_modulo'=>'labor'],
            ['id'=> 13,'nombre_modulo'=>'reporte-rotaciones'],
            ['id'=> 14,'nombre_modulo'=>'reporte-semana'],
            ['id'=> 15,'nombre_modulo'=>'reporte-marcas'],
            ['id'=> 16,'nombre_modulo'=>'marcas-noche'],
            ['id'=> 17,'nombre_modulo'=>'rango'],
            ['id'=> 18,'nombre_modulo'=>'configuracion']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulo');
    }
}
