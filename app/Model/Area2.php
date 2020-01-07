<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area2 extends Model
{
    protected $table="ACTIVIDAD";
    protected $connection= "sqlsrv";
    // protected $primaryKey = 'IDACTIVIDAD';

    public function labores()
    {
        return $this->hasMany('App\Model\Labor','IDACTIVIDAD','IDACTIVIDAD')
            ->selectRaw('RTRIM(DESCRIPCION) as nom_labor,IDLABOR as codigo,IDACTIVIDAD');
    }
}
