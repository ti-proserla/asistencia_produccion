<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    public function up()
    {
        Schema::table('area', function (Blueprint $table) {
            $table->string('id',3)->change(); 
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
