<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table="area";
    public function labores()
    {
        return $this->hasMany('App\Model\Labor');
    }
}
