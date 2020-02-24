<?php

namespace App\Http\Controllers;

use App\Model\Operador;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\HorasSemanaTrabajadorExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function turno(Request $request){
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        /**
         * Genera un array de palabras de busqueda
         */
        $texto_busqueda=explode(" ",$request->search);
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->leftJoin(DB::raw('(SELECT * FROM tareo WHERE WEEK(tareo.created_at,3)='.$request->week.' AND YEAR(tareo.created_at)='.$request->year.' GROUP BY operador_id) AS T'),'T.operador_id','=','operador.id')
            ->leftJoin('labor','labor.id','=','T.labor_id')
            ->leftJoin('area','area.id','=','labor.area_id')
            ->leftJoin('proceso','proceso.id','=','T.proceso_id')
            ->select(DB::raw("operador.dni As codigo, CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido, CONCAT(YEAR(ingreso),MONTH(ingreso),'-',WEEK(ingreso,3)) AS periodo, area.codigo As codActividad, labor.codigo AS codLabor, proceso.codigo As codProceso, labor.nom_labor,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Lunes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Martes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Miercoles, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Jueves, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Viernes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Sabado,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Domingo"))
            ->groupBy('codigo','procesos.operador.nom_operador','procesos.operador.ape_operador')
            ->where(DB::raw('WEEK(ingreso,3)'),$request->week)
            ->where(DB::raw('YEAR(ingreso)'),$request->year)
            ->where(DB::raw("CONCAT(dni,' ',nom_operador,' ',ape_operador)"),"like","%".$texto_busqueda[0]."%");
            for ($i=1; $i < count($texto_busqueda); $i++) { 
                $resultado=$resultado->where(DB::raw("CONCAT(dni,' ',nom_operador,' ',ape_operador)"),"like","%".$texto_busqueda[$i]."%");
            }
            $resultado=$resultado->get();
        return response()->json($resultado);
    }

    public function semana(Request $request){
        
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        /**
         * Genera un array de palabras de busqueda
         */
        $texto_busqueda=explode(" ",$request->search);
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }
        $week=str_pad($request->week, 2, "0", STR_PAD_LEFT);
        $year=$request->year;
        $planilla_id=$request->planilla_id;

        $query_turno="";
        if ($request->turno==null||$request->turno=="null") {
        }else{
            $query_turno="AND turno=".$request->turno;
        }
        $query="SELECT 	marcador.codigo_operador dni,
                        CONCAT(operador.nom_operador,' ',operador.ape_operador) NombreApellido,
                        DATE_FORMAT(fecha_ref, '%Y%m-%v') periodo,
                        T.area_id codActividad,
                        T.labor_id codLabor,
                        T.proceso_id codProceso,
                        labor.nom_labor,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=2 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Lunes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=3 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Martes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=4 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Miercoles,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=5 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Jueves,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=6 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Viernes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=7 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Sabado,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=1 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Domingo
                FROM 		marcador
                        LEFT JOIN (SELECT * FROM tareo GROUP BY codigo_operador,fecha) AS T 
                            on T.codigo_operador = marcador.codigo_operador and T.fecha = fecha_ref
                        LEFT JOIN labor on labor.id = T.labor_id
                        INNER JOIN operador on operador.dni = marcador.codigo_operador
                WHERE 		DATE_FORMAT(fecha_ref, '%Y-%v') = '$year-$week'
                        AND planilla_id=$planilla_id $query_busqueda
                        $query_turno 
                GROUP BY 	marcador.codigo_operador, codLabor";
        
        if ($request->has('excel')) {
            $raw_query=DB::select(DB::raw("$query"));
            return Excel::download(new HorasSemanaTrabajadorExport($raw_query), "horas-semana-$year-$week.xlsx");
        }else{
            $per_page=15;
            $current_page=$request->page;
            $total=DB::select(DB::raw("SELECT count(*) conteo FROM ($query) AL"))[0]->conteo;
            $last_page=(int)ceil($total/$per_page);
            $offset=($current_page-1)*$per_page;
            
            $raw_query=DB::select(DB::raw("$query limit $per_page offset $offset"));
            return response()->json([
                    "current_page"  =>  $current_page,
                    "data"          =>  $raw_query,
                    "total"         =>  $total,
                    "last_page"     =>  $last_page
                ]);
        }
    }
    public function pendientes(Request $request){
        $hoy=($request->has('fecha')) ? $request->fecha : Carbon::now()->format('Y-m-d');
        $resultado=Operador::join('marcador','marcador.codigo_operador','=','operador.dni')
            ->leftJoin(DB::raw("(SELECT * FROM tareo WHERE DATE(tareo.fecha)='".$hoy."') AS T"),'T.codigo_operador','=','operador.dni')
            ->where(DB::raw('DATE(marcador.ingreso)'),$hoy)
            ->where('marcador.turno',$request->turno)
            ->whereNull('T.id')
            ->groupBy('operador.dni')
            ->get();
        // dd($resultado);
            return response()->json($resultado);
    }
        
    public function marcas(Request $request){
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        /**
         * Genera un array de palabras de busqueda
         */
        $texto_busqueda=explode(" ",$request->search);

        $resultado=Operador::select(
                'dni',
                'nom_operador',
                'ape_operador',
                DB::raw('GROUP_CONCAT(CONCAT_WS("@",marcador.ingreso,marcador.salida) ORDER BY marcador.ingreso ASC SEPARATOR "@") AS marcas'),
                DB::raw('ROUND(SUM(TIMESTAMPDIFF(MINUTE,marcador.ingreso,IF(marcador.salida is null,marcador.ingreso,marcador.salida))/60 ),2) AS total')
            )->join('marcador','operador.dni','=','marcador.codigo_operador')
            ->where(DB::raw("fecha_ref"),$request->fecha)
            ->whereNotNull('ingreso')
            ->where(DB::raw("CONCAT(dni,' ',nom_operador,' ',ape_operador)"),"like","%".$texto_busqueda[0]."%")
            ->where('marcador.turno',$request->turno);
            for ($i=1; $i < count($texto_busqueda); $i++) { 
                $resultado=$resultado->where(DB::raw("CONCAT(dni,' ',nom_operador,' ',ape_operador)"),"like","%".$texto_busqueda[$i]."%");
            }
            $resultado=$resultado->groupBy('operador.dni')
            // ->get();
            ->paginate(8);
        return response()->json($resultado);
    }

    public function marcasPorCodigo(Request $request,$codigo){
        DB::select(DB::raw('select * from users where active = ?'
        
        
        
        ), [1]);
    }

    public function pendientesRegularizar(){
        $resultado=Operador::join('marcador','marcador.operador_id','=','operador.id')
            ->join('turno','turno.id','=','marcador.turno_id')
            ->whereNull('marcador.salida')
            ->select('operador.id as operador_id','operador.dni as dni','operador.nom_operador','operador.ape_operador','turno.id as turno_id','ingreso','salida','turno.descripcion')
            ->get();
        return response()->json($resultado);
    }

    public function rotaciones(Request $request){
        $datos  =   DB::select(
                        DB::raw("SELECT  labor.id as labor_id, nom_labor, 
                                    COUNT( CASE WHEN turno=1 THEN  codigo_operador ELSE null END) as turno_1,
                                    COUNT( CASE WHEN turno=2 THEN  codigo_operador ELSE null END) as turno_2
                                FROM labor 
                                INNER JOIN (
                                SELECT marcador.codigo_operador, marcador.ingreso, tareo.labor_id,turno 
                                FROM marcador INNER JOIN tareo on tareo.codigo_operador=marcador.codigo_operador
                                where DATE(ingreso) = ? AND DATE(tareo.fecha) = ?
                                GROUP BY marcador.codigo_operador
                                ) M on labor.id=M.labor_id
                                GROUP BY labor.id")
                    , [$request->fecha,$request->fecha]);
        return response()->json($datos);
    }
}
