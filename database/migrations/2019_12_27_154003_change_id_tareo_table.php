<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdTareoTable extends Migration
{
    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function up()
    {
        Schema::table('tareo', function (Blueprint $table) {
            $table->string('labor_id',6)->nullable()->change(); 
            $table->string('area_id',3)->nullable()->change(); 
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
