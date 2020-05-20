<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolModuloTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rol_modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rol_id');
            $table->integer('modulo_id');
        });

        DB::table('rol_modulo')->insert([
            ['rol_id'=> 1,'modulo_id'=>1],
            ['rol_id'=> 1,'modulo_id'=>2],
            ['rol_id'=> 1,'modulo_id'=>3],
            ['rol_id'=> 1,'modulo_id'=>4],
            ['rol_id'=> 1,'modulo_id'=>5],
            ['rol_id'=> 1,'modulo_id'=>6],
            ['rol_id'=> 1,'modulo_id'=>7],
            ['rol_id'=> 1,'modulo_id'=>8],
            ['rol_id'=> 1,'modulo_id'=>9],
            ['rol_id'=> 1,'modulo_id'=>10],
            ['rol_id'=> 1,'modulo_id'=>11],
            ['rol_id'=> 1,'modulo_id'=>12],
            ['rol_id'=> 1,'modulo_id'=>13],
            ['rol_id'=> 1,'modulo_id'=>14],
            ['rol_id'=> 1,'modulo_id'=>15],
            ['rol_id'=> 1,'modulo_id'=>16],
            ['rol_id'=> 1,'modulo_id'=>17],
            ['rol_id'=> 1,'modulo_id'=>18]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rol_modulo');
    }
}
