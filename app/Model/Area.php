<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table="area";
    // protected $connection= "sqlsrv";
    // protected $primaryKey = 'IDACTIVIDAD';

    public function labores()
    {
        return $this->hasMany('App\Model\Labor',"id","area_id")
            ->selectRaw('nom_labor,id as codigo');
    }
}
