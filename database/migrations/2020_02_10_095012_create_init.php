<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cuenta')->insert([
            [
                'nombre'    => 'Super', 
                'apellido'  => 'Usuario',
                'usuario'   => 'admin',
                'password'  => '12345678',
                'api_token' => 'dmf_desarrollo',
                'estado'    => '2',
                'rol'       => 'ADMINISTRADOR'
            ]
        ]);
        DB::table('planilla')->insert([
            "id"            => 1,
            "nom_planilla"  => "General",
            "salida"        => 7
        ]);
        DB::table('configuracion')->insert([
            'tiempo_entre_marcas'   => 45,
            'actividad'             => '',
            'ccosto'                => ''
        ]); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
