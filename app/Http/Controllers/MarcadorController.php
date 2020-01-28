<?php

namespace App\Http\Controllers;

use App\Model\Marcador;
use App\Model\Operador;
use App\Model\Turno;
use App\Model\Configuracion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarcadorController extends Controller
{
    public function index(Request $request){
        $marcas=Marcador::where('operador_id',$request->operador_id)
            ->where('turno_id',$request->turno_id)
            ->get();
        return response()->json($marcas);
    }
    /**
     * Evalua el DNI y Registra una marcacion
     */
    public function store(Request $request) 
    {
        // dd($request->all());

        $configuracion=Configuracion::first();
        /**
         * Creación de Operador
         */
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->planilla_id=null;
            $operador->save();
        }
            
        $marcador=Marcador::where('codigo_operador',$request->codigo_barras)
            ->orderBy('id','DESC')
            ->first();
        if ($marcador!=null) {
            $fecha_limite=Carbon::now()->subMinute($configuracion->tiempo_entre_marcas);
            if(($marcador->salida==null&&$fecha_limite<Carbon::parse($marcador->ingreso))||($marcador->salida!=null&&$fecha_limite<Carbon::parse($marcador->salida))) {
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente"
                    ]);
            }
        }

        
        /**
         * Parametros para Evaluacion y operacion
         */
        $hora_fecha_actual=Carbon::now();
        $hora_fecha_limite=Carbon::now()->startOfDay()->addHours($configuracion->hora_cierre_turno);
        $fecha_ayer=Carbon::now()->subDay()->format('Y-m-d');
        if (
            $marcador==null||
            $marcador->salida!=null||
            (
                $marcador->salida==null&&
                $fecha_ayer==Carbon::parse($marcador->ingreso)->format('Y-m-d')&&
                $hora_fecha_actual>$hora_fecha_limite
            )
        ) 
        {
            /**
             * Agregar Marca
             */
            $marcador=new Marcador();
            $marcador->codigo_operador=$operador->dni;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=null;
            $marcador->fundo_id=$request->fundo_id;
            $marcador->cuenta_id=$request->user_id;
            $marcador->save();
        }else{
            /**
             * Actualizar Marca
             */
            $marcador->salida=Carbon::now();
            $marcador->save();
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
        
    }

    
    public function update(Request $request,$id){
        // dd($request->all());
        $marcador=Marcador::where('id',$id)->first();
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
    
    public function consulta(Request $request){

    }
}
