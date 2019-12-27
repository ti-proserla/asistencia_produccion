<?php

namespace App\Http\Controllers;

use App\Model\Marcador;
use App\Model\Operador;
use App\Model\Turno;

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
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->planilla_id=null;
            $operador->save();
        }
            
        $marcador=Marcador::where('operador_id',$operador->id)
            ->where("turno_id",$request->turno_id)
            ->orderBy('id','DESC')
            ->first();
        if ($marcador!=null) {
            $fecha_limite=Carbon::now()->subMinute(10);
            if(($marcador->salida==null&&$fecha_limite<Carbon::parse($marcador->ingreso))||($marcador->salida!=null&&$fecha_limite<Carbon::parse($marcador->salida))) {
                return response()->json([
                    "status"    =>  "ERROR",
                    "data"      =>  "Usted marco recientemente"
                ]);
            }
        }
        if ($marcador==null||$marcador->salida!=null) {
            $turno=Turno::where('id',$request->turno_id)->first();
            $marcador=new Marcador();
            $marcador->operador_id=$operador->id;
            $marcador->turno_id=$request->turno_id;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=null;
            $marcador->save();
        }else{
            $marcador->salida=Carbon::now();
            $marcador->save();
        }
        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
        
    }

    public function store2(Request $request){
        /**
         * Comprobar Existencia y creación del operador
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


        $marcador=Marcador::where('operador_id',$operador->id)
            ->orderBy('id','DESC')
            ->first();
        
        $parametros_turno_1="6:00";

        $parametros_turno_2="16:00";

        
        $hora_fecha_actual=Carbon::now();

        $turno=null;

        $limite_1=Carbon::now()->startOfDay()
            ->addHours(explode(":",$parametros_turno_1)[0])
            ->addMinutes((int)$parametros_turno_1[1]);
        $limite_2=Carbon::now()->startOfDay()
            ->addHours(explode(":",$parametros_turno_2)[0])
            ->addMinutes((int)$parametros_turno_2[1]);
            

        if ($hora_fecha_actual<$limite_1) {
            dd("antes de las 6");
        }else if ($limite_1<=$hora_fecha_actual&&$hora_fecha_actual<$limite_2) {
            dd("turno 1");
            
        }else{
            dd("despues de las 4pm");
        }
        $fecha_actual=Carbon::now()->startOfDay();
        
        dd($fecha_actual,$fecha_limite);
        
        
        $turno=Turno::where('fecha',$fecha_actual)->first();
        if ($turno==null) {
            $turno=new Turno();
            $turno->descripcion=Carbon::parse($fecha_actual)->format('Ymd')."TURNO";
            $turno->fecha=$fecha_actual;
            $turno->save();
        }

        if ($marcador==null) {
            $marcador=new Marcador();
            $marcador->operador_id=$operador->id;
            $marcador->turno_id=$turno->id;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=null;
            $marcador->save();
        }else{
            $fecha_limite=Carbon::now()->subMinute(100);
            if(($marcador->salida==null&&$fecha_limite<Carbon::parse($marcador->ingreso))||($marcador->salida!=null&&$fecha_limite<Carbon::parse($marcador->salida))) {
                return response()->json([
                    "status"    =>  "ERROR",
                    "data"      =>  "Usted marco recientemente"
                ]);
            }            
                $marcador->salida=Carbon::now();
                $marcador->save();
                dd(json_encode($marcador),$turno,$fecha_actual);
            
        }
        

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
