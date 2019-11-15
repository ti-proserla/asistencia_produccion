<?php

namespace App\Http\Controllers;

use App\Model\Operador;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function turno(){
        // $semana=Carbon::now()->weekOfYear();
        // dd($semana);
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->select(DB::raw('operador.dni,nom_operador,ape_operador,YEAR(ingreso) as year,WEEK(ingreso) as month, SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Lunes, SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Martes, SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Miercoles, SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Jueves, SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Viernes, SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Sabado,SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Domingo'))
            ->groupBy('year','operador.dni', 'month','nom_operador','ape_operador')
            ->where(DB::raw('WEEK(ingreso)'),45)
            ->get();
        return response()->json($resultado);
    }
}
