<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Labor extends Model
{
    protected $table="labor";
    public $timestamps = false;
    protected $casts = [ 'id' => 'string' ];
}
