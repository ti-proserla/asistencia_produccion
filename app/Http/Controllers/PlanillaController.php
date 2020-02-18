<?php

namespace App\Http\Controllers;

use App\Model\Planilla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\PlanillaValidate;

class PlanillaController extends Controller
{
    /**
     * Visualiza todos los Planillaes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $planillas=Planilla::all();
        }else{
            $planillas=Planilla::paginate(8);
        }
        return response()->json($planillas);
    }

    /**
     * Registra un nuevo Planilla
     */
    public function store(PlanillaValidate $request)
    {
        $planilla=new Planilla();
        $planilla->nom_planilla=$request->nom_planilla;
        $planilla->tiempo_entre_marcas=$request->tiempo_entre_marcas;
        $planilla->salida=$request->salida;
        $planilla->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Planilla Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Planilla
     */
    public function show($id)
    {
        $planilla=Planilla::where('id',$id)->first();
        return response()->json($planilla);
    }
        
    public function update(PlanillaValidate $request, $id)
    {
        $planilla=Planilla::where('id',$id)->first();
        $planilla->nom_planilla=$request->nom_planilla;     
        $planilla->tiempo_entre_marcas=$request->tiempo_entre_marcas;
        $planilla->salida=$request->salida; 
        $planilla->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Planilla Actualizada"
        ]);
    }
}
