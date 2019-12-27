<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labor2;
use App\Model\Labor;

class SincronizarController extends Controller
{
    public function labor(){
        $labores=Labor2::selectRaw('idlabor,idactividad,RTRIM(descripcion) as nom_labor')->get();
        foreach ($labores as $key => $labor) {
            $laborAnalisis=Labor::where('id',$labor->idlabor)
                ->orWhere('nom_labor',$labor->nom_labor)
                ->first();
            if ($laborAnalisis!=null) {
                dd("encontrado");
            }else {
                
            }    
            // dd($laborAnalisis);
            // if (condition) {
            //     # code...
            // }
        }
        dd($labores);
    }
}
