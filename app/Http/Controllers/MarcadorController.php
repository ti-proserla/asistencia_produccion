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
            ->where(DB::raw('DATE(ingreso)'),$request->fecha)
            ->get();
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
        $marcador=Marcador::where('codigo_operador',$request->codigo_barras)
            ->where('ingreso','>',DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)'))
            ->where('turno',$request->turno)
            ->orderBy('id','DESC')
            ->first();
        // dd($marcador);

        /**
         * Marca Anterior Encontrada ?
         */
        if ($marcador!=null) {

            /**
             * Filtro por Marcado reciente
             */
            $fecha_limite=Carbon::now()->subMinute($configuracion->tiempo_entre_marcas);

            if(
                ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
            ) {
                $min=0;
                if ($marcador->salida == null) {
                    $min=Carbon::now()->diffInMinutes(Carbon::parse($marcador->ingreso));
                }else {
                    $min=Carbon::now()->diffInMinutes(Carbon::parse($marcador->salida));
                }
                return response()->json([
                        "status"    =>  "ERROR",
                        "data"      =>  "Usted marco recientemente. (Disponible en $min min)"
                    ]);
            }

            $hora_fecha_actual=Carbon::now();
            $hora_fecha_limite=Carbon::now()->startOfDay()->addHours($salida);
            $fecha_ayer=Carbon::now()->subDay()->format('Y-m-d');

            if ( 
                $marcador->salida!=null||
                (
                    $marcador->salida==null&&
                    $fecha_ayer==Carbon::parse($marcador->ingreso)->format('Y-m-d')&&
                    $hora_fecha_actual>$hora_fecha_limite
                )
            ) {
                $marcador=new Marcador();
                $marcador->codigo_operador=$operador->dni;
                $marcador->ingreso=Carbon::now();
                $marcador->salida=null;
                $marcador->fundo_id=$request->fundo_id;
                $marcador->cuenta_id=$request->user_id;
                $marcador->turno=$request->turno;
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
            $marcador->save();
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador->nom_operador." ".$operador->ape_operador,
            "foto"  => $operador->foto
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
