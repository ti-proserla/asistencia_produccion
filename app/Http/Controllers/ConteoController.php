<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Conteo;
use Carbon\Carbon;

class ConteoController extends Controller
{
    public function nuevo(Request $request){
        $Conteo=new Conteo();
        $Conteo->cod_barras=$request->cod_barras;
        $Conteo->cod_linea=substr($request->configuracion,0,2);
        $Conteo->cod_producto=substr($request->configuracion,2,2);
        $Conteo->save();
        return response()->json([
            "status"    =>  "OK",
            "data"      =>  "Conteo Registrada"
        ]);
    }
    public function reporte(Request $request){
        $fi=Carbon::parse($request->fi);
        $ff=Carbon::parse($request->ff);
        $conteos=Conteo::where('created_at','>=',$fi)
            ->where('created_at','<=',$ff)->get();
        return response()->json($conteos);
    }
}
