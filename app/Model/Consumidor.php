<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Consumidor extends Model
{
    protected $table="CONSUMIDOR";
    protected $connection= "sqlsrv";
    protected $primaryKey = 'IDCONSUMIDOR';
}
