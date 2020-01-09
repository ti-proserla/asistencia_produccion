<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table="proceso";
    protected $casts = [ 'id' => 'string' ];

}
