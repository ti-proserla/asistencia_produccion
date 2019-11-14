<?php

namespace App\Http\Controllers;

use App\Model\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ActividadValidate;

class ActividadController extends Controller
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
     * Registra un nuevo actividad
     */
    public function store(ActividadValidate $request)
    {
        $actividad=new Actividad();
        $actividad->codigo=$request->codigo;
        $actividad->nom_actividad=$request->nom_actividad;
        $actividad->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Actividad Registrada"
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
