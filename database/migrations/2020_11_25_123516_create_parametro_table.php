<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametro', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion',100);
            $table->string('valor',100)->nullable();
            $table->timestamps();
        });
        DB::table('parametro')->insert([
            [
                'descripcion'    => 'bloqueo_marcador', 
                'valor'  => '0',
            ]
        ]);
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro');
    }
}
