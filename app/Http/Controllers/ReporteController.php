<?php

namespace App\Http\Controllers;

use App\Model\Operador;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function turno(Request $request){
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->leftJoin(DB::raw('(SELECT * FROM tareo WHERE WEEK(tareo.created_at,3)='.$request->week.' AND YEAR(tareo.created_at)='.$request->year.') AS T'),'T.operador_id','=','operador.id')
            ->leftJoin('labor','labor.id','=','T.labor_id')
            ->leftJoin('area','area.id','=','labor.area_id')
            ->leftJoin('proceso','proceso.id','=','T.proceso_id')
            ->select(DB::raw("operador.dni As codigo, CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido, CONCAT(YEAR(ingreso),MONTH(ingreso),'-',WEEK(ingreso,3)) AS periodo, area.codigo As codActividad, labor.codigo AS codLabor, proceso.codigo As codProceso, labor.nom_labor,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Lunes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Martes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Miercoles, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Jueves, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Viernes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Sabado,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Domingo"))
            ->groupBy('codigo','procesos.operador.nom_operador','procesos.operador.ape_operador')
            ->where(DB::raw('WEEK(ingreso,3)'),$request->week)
            ->where(DB::raw('YEAR(ingreso)'),$request->year)
            ->get();
        return response()->json($resultado);
    }

    public function pendientes(Request $request){
        $resultado=Operador::join('marcador','marcador.operador_id','=','operador.id')
            ->leftJoin(DB::raw('(SELECT * FROM tareo WHERE turno_id='.$request->turno_id.') AS T'),'T.operador_id','=','operador.id')
            ->where('marcador.turno_id',$request->turno_id)
            ->whereNull('T.id')
            ->groupBy('operador.dni')
            ->get();
            return response()->json($resultado);
        }
        
    public function marcas(Request $request){
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->select('dni','nom_operador','ape_operador',DB::raw('GROUP_CONCAT(CONCAT(marcador.ingreso,"@",marcador.salida) SEPARATOR "@") AS marcas'),DB::raw('ROUND(SUM(TIMESTAMPDIFF(MINUTE,marcador.ingreso,marcador.salida)/60 ),2) AS total'))
            ->where('marcador.turno_id',$request->turno_id)
            ->groupBy('operador.dni')
            ->get();
        return response()->json($resultado);
    }
}
