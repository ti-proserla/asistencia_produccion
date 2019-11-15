<?php

namespace App\Http\Controllers;

use App\Model\Marcador;
use App\Model\Operador;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarcadorController extends Controller
{
    /**
     * Visualiza todos los actividades
     */
    public function index()
    {
        $actividades=Actividad::paginate(8);
        return response()->json($actividades);
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
            if (Carbon::now()->subMinute(1)<Carbon::parse($marcador->salida)) {
                return response()->json([
                    "status"    =>  "ERROR",
                    "data"      =>  "Usted marco recientemente"
                ]);
            }
        }

        if ($marcador==null||(Carbon::parse($marcador->salida)!=Carbon::parse($marcador->ingreso))) {
            $marcador=new Marcador();
            $marcador->operador_id=$operador->id;
            $marcador->turno_id=$request->turno_id;
            $marcador->ingreso=Carbon::now();
            $marcador->salida=Carbon::now();
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
    
    /**
     * Visualiza datos de un solo actividad
     */
    public function show($id)
    {
        $actividad=Actividad::where('id',$id)->first();
        return response()->json($actividad);
    }
        
    public function update(ActividadValidate $request, $id)
    {
        $actividad=Actividad::where('id',$id)->first();
        $actividad->codigo=$request->codigo;
        $actividad->nom_actividad=$request->nom_actividad;      
        $actividad->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Actividad Actualizada"
        ]);
    }

    /**
     * Cambiar de estado a la actividad
     */
    public function estado($id)
    {
        $actividad=Actividad::where('id',$id)->first();
        $actividad->estado=($actividad->estado=='0') ? '1' : '0';
        $actividad->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Estado Actualizado"
        ]);
    }
}
