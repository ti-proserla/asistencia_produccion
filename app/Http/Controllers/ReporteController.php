<?php

namespace App\Http\Controllers;

use App\Model\Operador;
use App\Model\Planilla;
use App\Model\Fundo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\HorasSemanaTrabajadorExport;
use App\Exports\HorasNocturnasExport;
use App\Exports\MarcasTurnoExport;
use App\Model\NPeriodo;
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
        /**
         * Query Busqueda 
         */
        $texto_busqueda=explode(" ",$request->search);
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }
        /**
         * Query YYYY-MM
         */
        $week=str_pad($request->week, 2, "0", STR_PAD_LEFT);
        $year=$request->year;
        $planilla_id=$request->planilla_id;
        /**
         * Query Turno
         */
        $query_turno=($request->turno!=null) ? "AND turno=".$request->turno : "";
        /**
         * Query fundo WHERE
         */        
        $query_fundo=($request->fundo_id!=null) ? "AND marcador.fundo_id='$request->fundo_id'" : "";
        $query_fundo2=($request->fundo_id!=null) ? "WHERE tareo.fundo_id='$request->fundo_id'" : "";
        /**
         * Query
         */
        $query="SELECT 	marcador.codigo_operador dni,
                        CONCAT(operador.nom_operador,' ',operador.ape_operador) NombreApellido,
                        DATE_FORMAT( STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_ref,'%x%v'),' Thursday'),'%x%v %W') , '%x%m-%v' ) periodo,
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
                        LEFT JOIN (SELECT * FROM tareo $query_fundo2 GROUP BY codigo_operador,fecha) AS T 
                            on T.codigo_operador = marcador.codigo_operador and T.fecha = fecha_ref
                        LEFT JOIN labor on labor.id = T.labor_id
                        INNER JOIN operador on operador.dni = marcador.codigo_operador
                WHERE 		DATE_FORMAT(fecha_ref, '%Y-%v') = '$year-$week'
                        AND planilla_id=$planilla_id 
                        $query_busqueda
                        $query_turno 
                        $query_fundo
                GROUP BY 	marcador.codigo_operador, codLabor";
        
        if ($request->has('excel')) {
            $raw_query=DB::select(DB::raw("$query"));
            $planilla=Planilla::where('id',$request->planilla_id)->first();
            $nom_planilla=$planilla->nom_planilla;
            /**
             * Descripcion Turno
             */
            $descripcion_turno=($request->turno!=null) ? "-turno-".$request->turno : "" ;
            /**
             * Descripcion Fundo
             */
            $fundo=Fundo::where('id',$request->fundo_id)->first();
            $descripcion_fundo=($fundo!=null) ? "-".$fundo->nom_fundo : "";
            return Excel::download(new HorasSemanaTrabajadorExport($raw_query), "rpt-semana-$year-$week".$descripcion_fundo.$descripcion_turno."-$nom_planilla.xlsx");
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
    public function semana_partida(Request $request){
        /**
         * Query Busqueda 
         */
        $texto_busqueda=explode(" ",$request->search);
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }
        /**
         * Rango de Fechas y planilla_id
         */
        $fecha_inicio=$request->inicio;
        $fecha_fin=$request->fin;

        $periodo=NPeriodo::where(DB::raw("FORMAT(FECHA_INI,'yyyy-MM-dd')"),'=',$fecha_inicio)
                    ->select(DB::raw("PERIODO periodo, SEMANA semana"))
                    ->first();
        $periodo_s=$periodo->periodo.'-'.$periodo->semana;
        // dd($periodo);

        $planilla_id=$request->planilla_id;
        /**
         * Query Turno
         */
        $query_turno=($request->turno!=null) ? "AND turno=".$request->turno : "";
        /**
         * Query fundo WHERE
         */        
        $query_fundo=($request->fundo_id!=null) ? "AND marcador.fundo_id='$request->fundo_id'" : "";
        $query_fundo2=($request->fundo_id!=null) ? "WHERE tareo.fundo_id='$request->fundo_id'" : "";
        /**
         * Query
         */
        $query="SELECT 	marcador.codigo_operador dni,
                        CONCAT(operador.nom_operador,' ',operador.ape_operador) NombreApellido,
                        '$periodo_s' periodo,
                        IFNULL(T.area_id,C.area_id ) codActividad,
                        IFNULL(T.labor_id,C.labor_id ) codLabor,
                        IFNULL(T.proceso_id,C.proceso_id) codProceso,
                        IFNULL(labor.nom_labor,C.nom_cargo) nom_labor,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=2 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Lunes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=3 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Martes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=4 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Miercoles,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=5 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Jueves,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=6 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Viernes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=7 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Sabado,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=1 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Domingo
                FROM 		marcador
                        LEFT JOIN (SELECT * FROM tareo $query_fundo2 GROUP BY codigo_operador,fecha) AS T 
                            on T.codigo_operador = marcador.codigo_operador and T.fecha = fecha_ref
                        LEFT JOIN labor on labor.id = T.labor_id
                        INNER JOIN operador on operador.dni = marcador.codigo_operador
                        LEFT JOIN cargo AS C on operador.cargo_id=C.id
                WHERE   fecha_ref <= '$fecha_fin'
                        AND fecha_ref >='$fecha_inicio'
                        AND planilla_id=$planilla_id 
                        $query_busqueda
                        $query_turno 
                        $query_fundo
                GROUP BY 	marcador.codigo_operador, codLabor";
        
        if ($request->has('excel')) {
            $raw_query=DB::select(DB::raw("$query"));
            $planilla=Planilla::where('id',$request->planilla_id)->first();
            $nom_planilla=$planilla->nom_planilla;
            /**
             * Descripcion Turno
             */
            $descripcion_turno=($request->turno!=null) ? "-turno-".$request->turno : "" ;
            /**
             * Descripcion Fundo
             */
            $fundo=Fundo::where('id',$request->fundo_id)->first();
            $descripcion_fundo=($fundo!=null) ? "-".$fundo->nom_fundo : "";
            return Excel::download(new HorasSemanaTrabajadorExport($raw_query), "rpt-semana-$fecha_inicio-al-$fecha_fin".$descripcion_fundo.$descripcion_turno."-$nom_planilla.xlsx");
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
            ->where(DB::raw('DATE(marcador.ingreso)'),$hoy);
        if ($request->fundo_id!=null) {
            $resultado=$resultado->where('marcador.fundo_id',$request->fundo_id);
        }
        if ($request->turno!=null) {
            $resultado=$resultado->where('marcador.turno',$request->turno);
        }
        $resultado=$resultado->whereNull('T.id')
            ->groupBy('operador.dni')
            ->get();
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
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }

        /**
         * Query fundo WHERE
         */
        if ($request->fundo_id==null||$request->fundo_id=="null") {
            $query_fundo="";
        }else{
            $fundo_id=$request->fundo_id;
            $query_fundo="AND marcador.fundo_id='$fundo_id'";
        }
        /**
         * Query turno WHERE
         */
        $query_turno="";
        if ($request->turno==null||$request->turno=="null") {
        }else{
            $query_turno="AND turno=".$request->turno;
        }
        
        $query="SELECT 	dni,
                        CONCAT(operador.ape_operador,', ',operador.nom_operador) NombreApellido,
                        DATE_FORMAT( STR_TO_DATE(CONCAT(DATE_FORMAT(fecha_ref,'%x%v'),' Thursday'),'%x%v %W') , '%x%m-%v' ) periodo,
                        IFNULL(T.area_id,C.area_id ) codActividad,
                        IFNULL(T.labor_id,C.labor_id ) codLabor,
                        IFNULL(T.proceso_id,C.proceso_id) codProceso,
                        IFNULL(labor.nom_labor,C.nom_cargo) nom_labor,
                        GROUP_CONCAT(CONCAT_WS('@',marcador.ingreso,marcador.salida) ORDER BY marcador.ingreso ASC SEPARATOR '@') AS marcas, 
                        ROUND(SUM(TIMESTAMPDIFF(MINUTE,marcador.ingreso,IF(marcador.salida is null,marcador.ingreso,marcador.salida))/60 ),2) AS total,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=2 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Lunes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=3 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Martes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=4 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Miercoles,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=5 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Jueves,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=6 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Viernes,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=7 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Sabado,
                        ROUND( SUM( CASE WHEN DAYOFWEEK(fecha_ref)=1 THEN ( IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ) ELSE 0 END  ) , 2) as Domingo 
                FROM	operador 
                        INNER JOIN marcador on operador.dni = marcador.codigo_operador
                        LEFT JOIN (SELECT * FROM tareo GROUP BY codigo_operador,fecha) AS T
                            on T.codigo_operador = marcador.codigo_operador and T.fecha = fecha_ref 
                        LEFT JOIN labor on labor.id = T.labor_id
                        LEFT JOIN cargo AS C on C.id=operador.cargo_id
                where 	fecha_ref = ?  and ingreso is not null 
                        $query_busqueda 
                        $query_fundo
                        $query_turno
                        and operador.planilla_id = ? 
                group by dni ORDER BY NombreApellido ASC";
            
            if ($request->has('excel')) {
                /**
                 * Nombre de excel
                 */
                $nom_excel="";
                $turno=$request->turno;
                $fecha=$request->fecha;
                $planilla=Planilla::where('id',$request->planilla_id)->first();
                $nom_planilla=$planilla->nom_planilla;
                $nom_excel="turno-$turno-dia-$fecha-$nom_planilla.xlsx";
                $raw_query=DB::select(DB::raw("$query"),[
                    $request->fecha,$request->planilla_id       
                ]);
                if ($request->excel=='nisira') {
                    return Excel::download(new HorasSemanaTrabajadorExport($raw_query), "Nisira-".$nom_excel);
                }else{
                    return Excel::download(new MarcasTurnoExport($raw_query,$fecha),$nom_excel);
                }
            }else{
                /**
                 * Paginacion
                 */
                $per_page=15;
                $current_page=$request->page;
                $total=DB::select(DB::raw("SELECT count(*) conteo FROM ($query) AL"),[
                        $request->fecha,$request->planilla_id,$request->turno        
                        ])[0]->conteo;
                $last_page=(int)ceil($total/$per_page);
                $offset=($current_page-1)*$per_page;
    
                $raw_query=DB::select(DB::raw("$query limit $per_page offset $offset"),[
                    $request->fecha,$request->planilla_id,$request->turno
                    ]);
                return response()->json([
                        "current_page"  =>  $current_page,
                        "data"          =>  $raw_query,
                        "total"         =>  $total,
                        "last_page"     =>  $last_page
                    ]);
            }
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

    public function horas_nocturnas(Request $request){

        /**
         * Genera un array de palabras de busqueda
         */
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        $texto_busqueda=explode(" ",$request->search);
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }

        /**
         * Query fundo WHERE
         */
        if ($request->fundo_id==null||$request->fundo_id=="null") {
            $query_fundo="";
        }else{
            $fundo_id=$request->fundo_id;
            $query_fundo="AND fundo_id='$fundo_id'";
        }

        /**
         * YYYY-MM
         */
        // $fecha=$request->year.'-'.str_pad($request->week, 2, "0", STR_PAD_LEFT);
        
        $fecha_inicio=$request->inicio;
        $fecha_fin=$request->fin;

        $periodo=NPeriodo::where(DB::raw("FORMAT(FECHA_INI,'yyyy-MM-dd')"),'=',$fecha_inicio)
                    ->select(DB::raw("PERIODO periodo, SEMANA semana"))
                    ->first();
        $periodo_s=$periodo->periodo.'-'.$periodo->semana;

        /**
         * Query turno WHERE
         */
        $query_turno="";
        if ($request->turno==null||$request->turno=="null") {
        }else{
            $query_turno="AND turno=".$request->turno;
        }

        $query = "SELECT	O.dni,
                            CONCAT(O.nom_operador,' ',O.ape_operador) NombreApellido,
                            IFNULL(T.area_id,C.area_id ) codActividad,
                            IFNULL(T.labor_id,C.labor_id ) codLabor,
                            IFNULL(T.proceso_id,C.proceso_id) codProceso,
                            IFNULL(labor.nom_labor,C.nom_cargo) nom_labor,
                            M.marcas,
                            M.h_trabajadas,
                            M.fecha_ref,
                            ROUND( (	
                                    (TIME_TO_SEC(s) - TIME_TO_SEC(i))
                                    + TIME_TO_SEC( IF(i < '22:00', IF( '06:00' < i , i , '06:00' ) , '22:00' ) ) 
                                    - TIME_TO_SEC( IF(s < '22:00', IF( '06:00' < s , s , '06:00' ) , '22:00' ) )
                                    + IF(s<i, TIME_TO_SEC('24:00') - (TIME_TO_SEC('22:00')-TIME_TO_SEC('06:00')),0)
                            )/3600 , 2) h_nocturnas
                FROM (
                        SELECT 		codigo_operador,
                                    fecha_ref,
                                    GROUP_CONCAT(CONCAT_WS('@',marcador.ingreso,marcador.salida) ORDER BY marcador.ingreso ASC SEPARATOR '@') AS marcas, 
                                    DATE_FORMAT(MIN(ingreso),'%H:%i') i,
                                    DATE_FORMAT(MAX(salida),'%H:%i') s,
                                    ROUND( SUM(IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60)) , 2) h_Trabajadas
                        FROM 		marcador 
                        WHERE 		fecha_ref >= ? AND
                                    fecha_ref <= ?
                                    $query_fundo
                                    $query_turno
                        GROUP BY 	codigo_operador,fecha_ref
                ) M 
                INNER JOIN  operador O on O.dni=M.codigo_operador
                LEFT JOIN (SELECT * FROM tareo GROUP BY codigo_operador,fecha) AS T on T.codigo_operador = M.codigo_operador and T.fecha = fecha_ref 
                LEFT JOIN labor on labor.id = T.labor_id
                LEFT JOIN cargo AS C on C.id=O.cargo_id
                WHERE       O.planilla_id=?
                            $query_busqueda
                HAVING      h_nocturnas > 0
                ";
        if ($request->has('excel')) {
            $raw_query=DB::select(DB::raw("$query"),[$fecha_inicio,$fecha_fin,$request->planilla_id]);
            $planilla=Planilla::where('id',$request->planilla_id)->first();
            $nom_planilla=$planilla->nom_planilla;
            return Excel::download(new HorasNocturnasExport($raw_query), "rpt-horas-nocturnas-$fecha_inicio-a-$fecha_fin-$nom_planilla.xlsx");
        }else{
            $datos=$this->paginate($query,[$fecha_inicio,$fecha_fin,$request->planilla_id],15,$request->page);
        }
        return response()->json($datos);
    }

    public function rango(Request $request){
        /**
         * Genera un array de palabras de busqueda
         */
        // dd($request->all());
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        $texto_busqueda=explode(" ",$request->search);
        $query_busqueda="";
        for ($i=0; $i < count($texto_busqueda); $i++) { 
            $query_busqueda=$query_busqueda." AND CONCAT(dni,' ',nom_operador,' ',ape_operador) like '%".$texto_busqueda[$i]."%'";
        }

        /**
         * Query fundo WHERE
         */
        if ($request->fundo_id==null||$request->fundo_id=="null") {
            $query_fundo="";
        }else{
            $fundo_id=$request->fundo_id;
            $query_fundo="AND fundo_id='$fundo_id'";
        }
        /**
         * Query turno WHERE
         */
        $query_turno="";
        if ($request->turno==null||$request->turno=="null") {
        }else{
            $query_turno="AND turno=".$request->turno;
        }

        /**
         * YYYY-MM
         */
        // $fecha=$request->year.'-'.str_pad($request->week, 2, "0", STR_PAD_LEFT);
        $fecha_inicio=$request->fecha_inicio;
        $fecha_fin=$request->fecha_fin;

        $query = "SELECT	O.dni,
                            CONCAT(O.nom_operador,' ',O.ape_operador) NombreApellido,
                            IFNULL(T.area_id,C.area_id ) codActividad,
                            IFNULL(T.labor_id,C.labor_id ) codLabor,
                            IFNULL(T.proceso_id,C.proceso_id) codProceso,
                            IFNULL(labor.nom_labor,C.nom_cargo) nom_labor,
                            M.marcas,
                            M.h_trabajadas,
                            M.fecha_ref,
                            ROUND( (	
                                    (TIME_TO_SEC(s) - TIME_TO_SEC(i))
                                    + TIME_TO_SEC( IF(i < '22:00', IF( '06:00' < i , i , '06:00' ) , '22:00' ) ) 
                                    - TIME_TO_SEC( IF(s < '22:00', IF( '06:00' < s , s , '06:00' ) , '22:00' ) )
                                    + IF(s<i, TIME_TO_SEC('24:00') - (TIME_TO_SEC('22:00')-TIME_TO_SEC('06:00')),0)
                            )/3600 , 2) h_nocturnas
                FROM (
                        SELECT 		codigo_operador,
                                    fecha_ref,
                                    GROUP_CONCAT(CONCAT_WS('@',marcador.ingreso,marcador.salida) ORDER BY marcador.ingreso ASC SEPARATOR '@') AS marcas, 
                                    DATE_FORMAT(MIN(ingreso),'%H:%i') i,
                                    DATE_FORMAT(MAX(salida),'%H:%i') s,
                                    ROUND( SUM(IF(salida is null,0,TIMESTAMPDIFF(MINUTE,ingreso,salida)/60)) , 2) h_Trabajadas
                        FROM 		marcador 
                        WHERE 		fecha_ref >= ? AND
                                    fecha_ref <= ?
                                    $query_fundo
                                    $query_turno
                        GROUP BY 	codigo_operador,fecha_ref
                ) M 
                INNER JOIN  operador O on O.dni=M.codigo_operador
                LEFT JOIN (SELECT * FROM tareo GROUP BY codigo_operador,fecha) AS T on T.codigo_operador = M.codigo_operador and T.fecha = fecha_ref 
                LEFT JOIN labor on labor.id = T.labor_id
                LEFT JOIN cargo AS C on C.id=O.cargo_id
                WHERE       O.planilla_id=?
                            $query_busqueda
                ";
        if ($request->has('excel')) {
            $raw_query=DB::select(DB::raw("$query"),[
                $fecha_inicio,
                $fecha_fin,
                $request->planilla_id
            ]);
            $planilla=Planilla::where('id',$request->planilla_id)->first();
            $nom_planilla=$planilla->nom_planilla;
            return Excel::download(new HorasNocturnasExport($raw_query), "rpt-rango-$fecha_inicio-a-$fecha_fin-$nom_planilla.xlsx");
        }else{
            $datos=$this->paginate($query,[
                $fecha_inicio,
                $fecha_fin,
                $request->planilla_id
            ],15,$request->page);
        }
        return response()->json($datos);
    }

    public function paginate($query,$param,$per_page = 10,$page = 1){
        $total=DB::select(DB::raw("SELECT count(*) conteo FROM ($query) AL"),$param)[0]->conteo;
        $last_page=(int)ceil($total/$per_page);
        $offset=($page-1)*$per_page;
        
        $raw_query=DB::select(DB::raw("$query limit $per_page offset $offset"),$param);
        return [
                "current_page"  =>  $page,
                "data"          =>  $raw_query,
                "total"         =>  $total,
                "last_page"     =>  $last_page
            ];
    }

}

