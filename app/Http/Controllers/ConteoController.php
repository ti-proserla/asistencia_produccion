<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Model\Conteo;

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
        $conteos=Conteo::select(DB::raw('COUNT(cod_barras) as count, cod_linea'))
            ->where('created_at','>=',$fi)
            ->where('created_at','<=',$ff)
            ->groupBy('cod_linea')
            ->get();
        return response()->json($conteos);
    }

    public function reporteOperario(Request $request){
        $fi=Carbon::parse($request->fi);
        $ff=Carbon::parse($request->ff);
        $conteos=Conteo::select(DB::raw('COUNT(cod_barras) as count, cod_barras'))
            ->where('created_at','>=',$fi)
            ->where('created_at','<=',$ff)
            ->where(DB::raw('LENGTH(cod_barras)'),'=',8)
            ->groupBy('cod_barras')
            ->orderBy('count','desc')
            ->get();
        return response()->json($conteos);
    }
}
