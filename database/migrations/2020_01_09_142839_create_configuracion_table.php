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
            $table->string('nombre',50);
            $table->string('parametro',200)->nullable();
            $table->timestamps();
        });
        DB::table('configuracion')->insert([
            // ['nombre' => 'actividad', 'parametro' => ''],
            ['nombre' => 'actividad', 'parametro' => ''],
            ['nombre' => 'ccosto', 'parametro' => '']
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
