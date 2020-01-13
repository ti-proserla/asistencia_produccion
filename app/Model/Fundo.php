<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Fundo extends Model
{
    protected $table="fundo";
    public $timestamps = false;
    protected $casts = [ 'id' => 'string' ];

    public function procesos()
    {
        
        return $this->hasMany('App\Model\Proceso','fundo_id','id')
            ->selectRaw('*');
    }
}
