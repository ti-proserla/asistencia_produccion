<?php

namespace App\Http\Controllers;

use App\Model\Marcador;
use App\Model\Operador;
use App\Model\Planilla;
use App\Model\Turno;
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
        $marcas=$marcas->get();
        return response()->json($marcas);
    }
    /**
     * Evalua el DNI y Registra una marcacion
     */
    public function store(Request $request) 
    {    


        $configuracion=Configuracion::first();

        /**
         * Creación de Operador y Asignacion a la planilla General
         */
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
        $hora_fecha_actual=Carbon::now();
        $hora_fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
        $fecha_ayer=Carbon::now()->subDay()->format('Y-m-d');

        $fecha_consulta=Carbon::now()->format('Y-m-d');
        if ($hora_fecha_actual<$hora_fecha_limite) {
            $fecha_consulta=Carbon::now()->subDay()->format('Y-m-d');
        }
        

        $consulta_1=Marcador::where('codigo_operador',$request->codigo_barras)
            ->where('fecha_ref',$fecha_consulta)
            ->select('codigo_operador',DB::raw('min(ingreso) ingreso,MAX(id) id'))
            ->having('ingreso','>',DB::raw('DATE_SUB(NOW(), INTERVAL 15 HOUR)'))
            ->groupBy('codigo_operador')
            ->first();
        $marcador=null;
        if ($consulta_1!=null) {
            $marcador=Marcador::where('id',$consulta_1->id)->first();
        }
        // dd($marcador);
        // dd($marcador);

        /**
         * Marca Anterior Encontrada ?
         */
        if ($marcador==null) {
            
            /**
             * Agregar Marca
             */
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=null;
            $marcador->fundo_id=$request->fundo_id;
            $marcador->cuenta_id=$request->user_id;
            $marcador->turno=$request->turno;
            $marcador->fecha_ref=Carbon::now();
            $marcador->save();
        }else{

        

            /**
             * Filtro por Marcado reciente
             */
            $tiempo_entre_marcas=Planilla::where('id',$operador->planilla_id)->first()->tiempo_entre_marcas;
            $fecha_limite=Carbon::now()->subMinute($tiempo_entre_marcas);

            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas)->format('H:i');
                }else {
                    $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas)->format('H:i');
                }
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente. (Proxima marca $min)"
                    ]);
            }

            
            if ( 
                $marcador->salida!=null||
                (
                    $marcador->salida==null&&
                    $fecha_ayer==Carbon::parse($marcador->ingreso)->format('Y-m-d')&&
                    $hora_fecha_actual>$hora_fecha_limite
                )
            ) {
                $newMarcador=$marcador;
                
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=Carbon::now();
                $marcador->salida=null;
                $marcador->fundo_id=$request->fundo_id;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
                $marcador->fecha_ref=$newMarcador->fecha_ref;
                $marcador->save();
            }else{
                $marcador->salida=Carbon::now();
                $marcador->save();
            }
            
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador->nom_operador." ".$operador->ape_operador,
            "foto"  => $operador->foto
        ]);
        
    }

    public function store2(Request $request) 
    {    
        $configuracion=Configuracion::first();

        /**
         * Creación de Operador y Asignacion a la planilla General
         */
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
        $hora_fecha_actual=Carbon::now();
        $hora_fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
        $fecha_ayer=Carbon::now()->subDay()->format('Y-m-d');
        
        $marcador=Marcador::where('codigo_operador',$request->codigo_barras)
            ->where('ingreso','>',DB::raw('DATE_SUB(NOW(), INTERVAL 15 HOUR)'))
            ->orderBy('ingreso','DESC')
            ->first();
        $fecha_consulta=Carbon::now()->format('Y-m-d');
        if ($hora_fecha_actual<$hora_fecha_limite) {
            $fecha_consulta=Carbon::now()->subDay()->format('Y-m-d');
        }
        /**
         * Marca Anterior Encontrada ?
         */
        if ($marcador!=null) {

            /**
             * Filtro por Marcado reciente
             */
            $tiempo_entre_marcas=Planilla::where('id',$operador->planilla_id)->first()->tiempo_entre_marcas;
            $fecha_limite=Carbon::now()->subMinute($tiempo_entre_marcas);
            // dd($fecha_limite);
            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas)->format('H:i');
                }else {
                    $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas)->format('H:i');
                }
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente. (Proxima marca $min)"
                    ]);
            }

            
            if ( 
                $marcador->salida!=null||
                (
                    $marcador->salida==null&&
                    $fecha_ayer==Carbon::parse($marcador->fecha_ref)->format('Y-m-d')&&
                    $hora_fecha_actual>$hora_fecha_limite
                )
            ) {
                $newMarcador=$marcador;
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=Carbon::now();
                $marcador->salida=null;
                $marcador->fundo_id=$request->fundo_id;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
                $marcador->fecha_ref=($hora_fecha_actual>$hora_fecha_limite) ? Carbon::now() : $newMarcador->fecha_ref;
                $marcador->save();
            }else{
                $marcador->salida=Carbon::now();
                $marcador->save();
            }
            
        }else{
            /**
             * Agregar Marca
             */
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=null;
            $marcador->fundo_id=$request->fundo_id;
            $marcador->cuenta_id=$request->user_id;
            $marcador->turno=$request->turno;
            $marcador->fecha_ref=Carbon::now();
            $marcador->save();
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
        // dd($fecha_ref,$fecha_siguiente);
        // /**
        //  * Condicion turno
        //  */
        // // $fecha_ref=Carbon::parse($fecha_ref);
        // if (Carbon::parse($fec)) {
            
        // }
        // if ($marcador->turno==2) {
            
        // }
        
        
        $marcador->ingreso=($request->ingreso == null||$request->salida == 'Invalid date') ? $marcador->ingreso : $request->ingreso;
        $marcador->salida=($request->salida == null||$request->salida == 'Invalid date') ?  $request->salida: $request->salida;
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
        $marcador=new Marcador();
        $marcador->codigo_operador=$request->codigo_operador;
        $marcador->cuenta_id=$request->user_id;
        $marcador->turno=($request->turno==null)?1:$request->turno;
        $marcador->fecha_ref=$request->fecha;
        $marcador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Marca Agregada"
        ]);
    }

    public function remove(Request $request){
        $marcador=Marcador::where('id',$request->marcador_id)->first();
        $marcador->delete();
        return response()->json([
            "status"=> "OK",
            "data"  => "Marca Eliminada"
        ]);
    }
}
