<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Operador extends Model
{
    protected $table='operador';
    public function marcas()
    {
        return $this->hasOne('App\Model\Marcador');
    }
    public function cargo(){
        return $this->belongsTo('App\Model\Cargo');
    }
}
