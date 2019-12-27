<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdLaborTable extends Migration
{
    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function up()
    {
        Schema::table('labor', function (Blueprint $table) {
            $table->string('id',6)->change(); 
            $table->string('area_id',3)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
