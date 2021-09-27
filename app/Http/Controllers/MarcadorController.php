<?php

namespace App\Http\Controllers;

use App\Model\Marcador;
use App\Model\Tareo;
use App\Model\Operador;
use App\Model\Planilla;
use App\Model\Turno;
use App\Model\Parametro;
use App\Model\Configuracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarcadorController extends Controller
{
    public function index(Request $request){
        $marcas=Marcador::where('codigo_operador',$request->codigo_operador)
            ->where(DB::raw('DATE(fecha_ref)'),$request->fecha);
        if ($request->turno!=null) {
            $marcas=$marcas->where('turno',$request->turno);
        }
        if ($request->fundo_id!=null) {
            $marcas=$marcas->where('fundo_id',$request->fundo_id);
        }
        $marcas=$marcas->get();
        return response()->json($marcas);
    }
    /**
     * Evalua el DNI y Registra una marcacion
     */
    public function store(Request $request) 
    {   
        
        if ($request->codigo_barras=="00000001") {
            $parametro=Parametro::where('descripcion','bloqueo_marcador')->first();
            $parametro->valor=$parametro->valor=='0' ? '1' : '0';
            $parametro->save();

            if ($parametro->valor=='0') {
                return response()->json([
                    "status"=> "OK",
                    "data"  => "Marcador desbloqueado.",
                    "foto"  => null
                    ]);
            }else{
                return response()->json([
                    "status"    =>  "ERROR",
                    "data"      =>  "Marcador Bloqueado."
                ]);
            }
        }
        
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->planilla_id=1;
            $operador->save();
        }
        $planilla_id=0;
        if ($operador->planilla_id==null) {
            $planilla_id=1;
        }else {
            $planilla_id=$operador->planilla_id;
        }

        $valor=Parametro::where('descripcion','bloqueo_marcador')->first()->valor;
        if ($valor == '1' && $planilla_id=1) {
            return response()->json([
                "status"    =>  "ERROR",
                "data"      =>  "Marcador Bloqueado, Notificar a RRHH."
            ]);
        }


        $salida=Planilla::where('id',$planilla_id)->first()->salida;
        $fecha_analisis=Carbon::now();
        $fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
        
        $fecha_consulta=($fecha_analisis<$fecha_limite) ? Carbon::now()->subDay()->format('Y-m-d') : $fecha_consulta=Carbon::now()->format('Y-m-d');

        $consulta_1=Marcador::where('codigo_operador',$request->codigo_barras)
            ->where('fecha_ref','>=',$fecha_consulta)
            ->where('turno','=',$request->turno)
            ->where('fecha_ref','<=',Carbon::now()->format('Y-m-d'))
            ->select('codigo_operador','fecha_ref',DB::raw('min(ingreso) ingreso,MAX(id) id'))
            ->having('ingreso','>',DB::raw('DATE_SUB("'.Carbon::now().'", INTERVAL 16 HOUR)'))
            ->groupBy('codigo_operador','fecha_ref')
            ->first();

            
        $marcador=null;
        
        if ($consulta_1!=null) {
            $marcador=Marcador::where('id',$consulta_1->id)->first();
        }

        if ($marcador!=null) { // En caso sea su primera marca en todo el sistema.
            $tiempo_entre_marcas=Planilla::where('id',$planilla_id)->first()->tiempo_entre_marcas;
            $fecha_limite=Carbon::now()->subMinute($tiempo_entre_marcas);
            
            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }else {
                    $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente. (Proxima marca $min)"
                    ]);
            }
        }
        
        if (is_null($marcador)) {
            
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=$fecha_analisis;
            $marcador->salida=null;
            $marcador->turno=$request->turno;
            $marcador->cuenta_id=$request->user_id;
            $marcador->fecha_ref=$fecha_analisis;
            $marcador->tareo_id=null;
            $marcador->save();
            $fecha=$fecha_analisis->format('Y-m-d');
            // dd($fecha);
            $tareo_anterior=Tareo::where('fecha',$fecha)
                        ->where('turno_id',$request->turno)
                        ->where('codigo_operador',$operador->dni)
                        ->orderBy('id','DESC')
                        ->first();
            if ($tareo_anterior!=null) {
                $marcador->tareo_id=$tareo_anterior->id;
                $marcador->save();
            }

        }else{
            if (!is_null($marcador->salida)) {
                $newMarcador=$marcador;
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=$fecha_analisis;
                $marcador->salida=null;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
                $marcador->tareo_id=null;
                $marcador->fecha_ref=$newMarcador->fecha_ref;
                $marcador->save();
                $tareo_anterior=Tareo::where('fecha',$newMarcador->fecha_ref)
                        ->where('turno_id',$request->turno)
                        ->where('codigo_operador',$operador->dni)
                        ->orderBy('id','DESC')
                        ->first();
                if ($tareo_anterior!=null) {
                    $marcador->tareo_id=$tareo_anterior->id;
                    $marcador->save();
                }

            }else{
                $marcador->salida=$fecha_analisis;
                $marcador->save();
                $tareo_anterior=Tareo::where('fecha',$marcador->fecha_ref)
                        ->where('turno_id',$request->turno)
                        ->where('codigo_operador',$operador->dni)
                        ->orderBy('id','DESC')
                        ->first();
                if ($tareo_anterior!=null) {
                    $marcador->tareo_id=$tareo_anterior->id;
                    $marcador->save();
                }
            }
            
        }

        /**
         * 
         */
        /**
         * 
         */
        // $configuracion=Configuracion::first();

        // /**
        //  * Creación de Operador y Asignacion a la planilla General
        //  */
        // $operador=Operador::where('dni',$request->codigo_barras)->first();
        // if ($operador==null) {
        //     $operador=new Operador();
        //     $operador->dni=$request->codigo_barras;
        //     $operador->nom_operador="Nuevo";
        //     $operador->ape_operador="Trabajador";
        //     $operador->planilla_id=1;
        //     $operador->save();
        // }
        
        // $salida=Planilla::where('id',$operador->planilla_id)->first()->salida;
        // $hora_fecha_actual=Carbon::now();
        // $hora_fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
        // $fecha_ayer=Carbon::now()->subDay()->format('Y-m-d');

        // $fecha_consulta=Carbon::now()->format('Y-m-d');
        // if ($hora_fecha_actual<$hora_fecha_limite) {
        //     $fecha_consulta=Carbon::now()->subDay()->format('Y-m-d');
        // }
        

        // $consulta_1=Marcador::where('codigo_operador',$request->codigo_barras)
        //     ->where('fecha_ref',$fecha_consulta)
        //     ->select('codigo_operador',DB::raw('min(ingreso) ingreso,MAX(id) id'))
        //     ->having('ingreso','>',DB::raw('DATE_SUB(NOW(), INTERVAL 16 HOUR)'))
        //     ->groupBy('codigo_operador')
        //     ->first();

        // $marcador=null;

        // if ($consulta_1!=null) {
        //     $marcador=Marcador::where('id',$consulta_1->id)->first();
        // }

        
        // /**
        //  * Inicio de algoritmo de comprobacion de Ultima Marca.
        //  */
        // $ultimo_par_marca=$marcador;
        
        // if ($ultimo_par_marca==null) {
        //     $ultimo_par_marca=Marcador::where('codigo_operador',$request->codigo_barras)
        //     ->orderBy('ingreso','DESC')
        //     ->first();
        // }

        // if ($ultimo_par_marca!=null) { // En caso sea su primera marca en todo el sistema.
        //     $tiempo_entre_marcas=Planilla::where('id',$operador->planilla_id)->first()->tiempo_entre_marcas;
        //     $fecha_limite=Carbon::now()->subMinute($tiempo_entre_marcas);

        //     if(
        //         ( $ultimo_par_marca->salida == null && $fecha_limite < Carbon::parse($ultimo_par_marca->ingreso) ) ||
        //         ( $ultimo_par_marca->salida != null && $fecha_limite < Carbon::parse($ultimo_par_marca->salida) )
        //     ) {
        //         $min=0;
        //         if ($ultimo_par_marca->salida == null) {
        //             $min=Carbon::parse($ultimo_par_marca->ingreso)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
        //         }else {
        //             $min=Carbon::parse($ultimo_par_marca->salida)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
        //         }
        //         return response()->json([
        //                 "status"    =>  "ERROR",
        //                 "data"      =>  "Usted marco recientemente. (Proxima marca $min)"
        //             ]);
        //     }
        // }
        // /**
        //  * Fin de algoritmo de ultima marca.
        //  */ 

        // /**
        //  * Marca Anterior Encontrada ?
        //  */
        // if ($marcador==null) {
            
        //     /**
        //      * Agregar Marca
        //      */
        //     $marcador=new Marcador();
        //     $marcador->codigo_operador=$operador->dni;
        //     $marcador->ingreso=Carbon::now();
        //     $marcador->salida=null;
        //     $marcador->fundo_id=$request->fundo_id;
        //     $marcador->cuenta_id=$request->user_id;
        //     $marcador->turno=$request->turno;
        //     $marcador->fundo_id=$request->fundo;
        //     $marcador->fecha_ref=Carbon::now();
        //     $marcador->save();
        // }else{

        //     if ( 
        //         $marcador->salida!=null||
        //         (
        //             $marcador->salida==null&&
        //             $fecha_ayer==Carbon::parse($marcador->ingreso)->format('Y-m-d')&&
        //             $hora_fecha_actual>$hora_fecha_limite
        //         )
        //     ) {
        //         $newMarcador=$marcador;
                
        //         $marcador=new Marcador();
        //         $marcador->codigo_operador=$operador->dni;
        //         $marcador->ingreso=Carbon::now();
        //         $marcador->salida=null;
        //         $marcador->fundo_id=$request->fundo_id;
        //         $marcador->cuenta_id=$request->user_id;
        //         $marcador->turno=$request->turno;
        //         $marcador->fecha_ref=$newMarcador->fecha_ref;
        //         $marcador->save();
        //     }else{
        //         $marcador->salida=Carbon::now();
        //         $marcador->save();
        //     }
            
        // }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador->nom_operador." ".$operador->ape_operador." (".$request->codigo_barras.")",
            "foto"  => $operador->foto
        ]);
        
    }

    public function storeOffline(Request $request){
        // dd($request->all());
        /**
         * Creación de Operador y Asignacion a la planilla General
         */
        $operador=Operador::where('dni',$request->codigo)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->planilla_id=1;
            $operador->save();
        }
        
        $salida=Planilla::where('id',$operador->planilla_id)->first()->salida;
        $fecha_analisis=Carbon::parse($request->fecha);
        $fecha_limite=Carbon::parse($request->fecha)->startOfDay()->addHours($salida);
        
        $fecha_consulta=($fecha_analisis<$fecha_limite) ? Carbon::parse($request->fecha)->subDay()->format('Y-m-d') : $fecha_consulta=Carbon::parse($request->fecha)->format('Y-m-d');

        $consulta_1=Marcador::where('codigo_operador',$request->codigo)
            ->where('fundo_id','=',$request->fundo)
            ->where('fecha_ref','>=',$fecha_consulta)
            ->where('fecha_ref','<=',Carbon::parse($request->fecha)->format('Y-m-d'))
            ->select('codigo_operador','fecha_ref',DB::raw('min(ingreso) ingreso,MAX(id) id'))
            ->having('ingreso','>',DB::raw('DATE_SUB("'.Carbon::parse($request->fecha).'", INTERVAL 16 HOUR)'))
            ->groupBy('codigo_operador','fecha_ref')
            ->first();

            
        $marcador=null;
        
        if ($consulta_1!=null) {
            $marcador=Marcador::where('id',$consulta_1->id)->first();
        }

        if ($marcador!=null) { // En caso sea su primera marca en todo el sistema.
            $tiempo_entre_marcas=Planilla::where('id',$operador->planilla_id)->first()->tiempo_entre_marcas;
            $fecha_limite=Carbon::parse($request->fecha)->subMinute($tiempo_entre_marcas);
            
            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }else {
                    $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }
                return response()->json([
                    "status"=> "OK",
                    "data"  => $request->rowid
                ]);
            }
        }
        
        if (is_null($marcador)) {
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=$fecha_analisis;
            $marcador->salida=null;
            $marcador->fundo_id=$request->fundo;
            $marcador->cuenta_id=$request->user_id;
            $marcador->fundo_id=$request->fundo;
            $marcador->fecha_ref=$fecha_analisis;
            $marcador->save();
        }else{
            if (!is_null($marcador->salida)) {
                $newMarcador=$marcador;
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=$fecha_analisis;
                $marcador->salida=null;
                $marcador->fundo_id=$request->fundo;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
                $marcador->fecha_ref=$newMarcador->fecha_ref;
                $marcador->save();
            }else{
                $marcador->salida=$fecha_analisis;
                $marcador->save();
            }
            
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $request->rowid
        ]);
    }

    public function store2(Request $request) 
    {    
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->planilla_id=1;
            $operador->save();
        }
        
        $salida=Planilla::where('id',$operador->planilla_id)->first()->salida;
        $fecha_analisis=Carbon::now();
        $fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
        
        $fecha_consulta=($fecha_analisis<$fecha_limite) ? Carbon::now()->subDay()->format('Y-m-d') : $fecha_consulta=Carbon::now()->format('Y-m-d');

        $consulta_1=Marcador::where('codigo_operador',$request->codigo_barras)
            ->where('fundo_id','=',$request->fundo)
            ->where('fecha_ref','>=',$fecha_consulta)
            ->where('fecha_ref','<=',Carbon::now()->format('Y-m-d'))
            ->select('codigo_operador','fecha_ref',DB::raw('min(ingreso) ingreso,MAX(id) id'))
            ->having('ingreso','>',DB::raw('DATE_SUB("'.Carbon::now().'", INTERVAL 16 HOUR)'))
            ->groupBy('codigo_operador','fecha_ref')
            ->first();

            
        $marcador=null;
        
        if ($consulta_1!=null) {
            $marcador=Marcador::where('id',$consulta_1->id)->first();
        }

        if ($marcador!=null) { // En caso sea su primera marca en todo el sistema.
            $tiempo_entre_marcas=Planilla::where('id',$operador->planilla_id)->first()->tiempo_entre_marcas;
            $fecha_limite=Carbon::now()->subMinute($tiempo_entre_marcas);
            
            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }else {
                    $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                }
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente. (Proxima marca $min)"
                    ]);
            }
        }
        
        if (is_null($marcador)) {
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=$fecha_analisis;
            $marcador->salida=null;
            $marcador->fundo_id=$request->fundo_id;
            $marcador->cuenta_id=$request->user_id;
            $marcador->fundo_id=$request->fundo;
            $marcador->fecha_ref=$fecha_analisis;
            $marcador->save();
        }else{
            if (!is_null($marcador->salida)) {
                $newMarcador=$marcador;
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=$fecha_analisis;
                $marcador->salida=null;
                $marcador->fundo_id=$request->fundo_id;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
                $marcador->fecha_ref=$newMarcador->fecha_ref;
                $marcador->save();
            }else{
                $marcador->salida=$fecha_analisis;
                $marcador->save();
            }
            
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador->nom_operador." ".$operador->ape_operador,
            "foto"  => $operador->foto
        ]);
    }
    
    public function update(Request $request,$id){
        $marcador=Marcador::where('id',$id)->first();
        $fecha_ref=$marcador->fecha_ref;
        $fecha_siguiente=Carbon::parse($fecha_ref)->addDay()->format("Y-m-d");
        
        // /**
        //  * Fuera de semana
        //  */
        // if (Carbon::now()->startOfWeek()->addHours(12)<Carbon::now()) {
        //     if (Carbon::now()->startOfWeek()>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible editar"
        //         ]);
        //     }
        // }else{
        //     if (Carbon::now()->startOfWeek()->subDays(7)>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible editar"
        //         ]);
        //     }
        // }
        
        /**
         * Evaluacion de fechas
         */
        $ini=($request->ingreso == null || $request->ingreso == 'Invalid date') ? null: Carbon::parse($request->ingreso)->format("Y-m-d");
        $fin=($request->salida == null || $request->salida == 'Invalid date') ? null: Carbon::parse($request->salida)->format("Y-m-d");
        $prueba=!($fecha_ref==$ini || $fecha_siguiente==$ini||$ini==null) || !($fecha_ref==$fin || $fecha_siguiente==$fin||$fin==null);

        if ($prueba) {
            return response()->json([
                "status"=> "error",
                "data"  => "Fecha no permitida, fecha de referencia ".$fecha_ref
            ]);    
        }
        
        $marcador->ingreso = ( $request->ingreso == null || $request->ingreso == 'Invalid date' ) ? $marcador->ingreso : $request->ingreso;
        $marcador->salida  = ( $request->salida == null || $request->salida == 'Invalid date' ) ?  $request->salida: $request->salida;
        
        // Carbon::parse();
        // if ($fecha_ref==Carbon::parse()) {
        //     # code...
        // }
        $marcador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Marca actualizada"
        ]);
    }
    /**
     * Visualiza datos de un solo actividad
     */
    public function show($id)
    {
        $actividad=Actividad::where('id',$id)->first();
        return response()->json($actividad);
    }
    
    public function add(Request $request){
        $fecha_ref=$request->fecha;
        
        // /**
        //  * Fuera de semana
        //  */
        // if (Carbon::now()->startOfWeek()->addHours(12)<Carbon::now()) {
        //     if (Carbon::now()->startOfWeek()>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible Agregar"
        //         ]);
        //     }
        // }else{
        //     if (Carbon::now()->startOfWeek()->subDays(7)>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible Agregar"
        //         ]);
        //     }
        // }
        $marcador=new Marcador();
        $marcador->codigo_operador=$request->codigo_operador;
        $marcador->cuenta_id=$request->user_id;
        $marcador->turno=($request->turno==null) ? null:$request->turno;
        $marcador->fundo_id=($request->fundo_id==null) ? null:$request->fundo_id;
        $marcador->fecha_ref=$request->fecha;
        $marcador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Marca Agregada"
        ]);
    }

    public function remove(Request $request){
        $marcador=Marcador::where('id',$request->marcador_id)->first();
        $fecha_ref=$marcador->fecha_ref;
        
        // /**
        //  * Fuera de semana
        //  */
        // if (Carbon::now()->startOfWeek()->addHours(12)<Carbon::now()) {
        //     if (Carbon::now()->startOfWeek()>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible Eliminar."
        //         ]);
        //     }
        // }else{
        //     if (Carbon::now()->startOfWeek()->subDays(7)>Carbon::parse($fecha_ref)) {
        //         return response()->json([
        //             "status"=> "error",
        //             "data"  => "Fecha ".$fecha_ref." cerrada. No es posible Eliminar."
        //         ]);
        //     }
        // }

        $marcador->delete();
        return response()->json([
            "status"=> "OK",
            "data"  => "Marca Eliminada"
        ]);
    }
}
