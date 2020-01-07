<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table="area";
    public $timestamps = false;
    protected $casts = [ 'id' => 'string' ];

    public function labores()
    {
        return $this->hasMany('App\Model\Labor','area_id','id')
            ->selectRaw('*');
    }
}
