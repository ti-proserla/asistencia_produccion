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
            $operador->save();
        }
            
        $marcador=Marcador::where('operador_id',$operador->id)
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
            // dd(($turno->fecha==Carbon::now()->format('Y-m-d')));
            if ($turno->fecha!=Carbon::now()->format('Y-m-d')) {
                return response()->json([
                    "status"    =>  "ERROR",
                    "data"      =>  "Turno no corresponde al día actual."
                ]);
            }
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
