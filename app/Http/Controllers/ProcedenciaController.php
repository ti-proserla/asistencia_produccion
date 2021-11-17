<?php

namespace App\Http\Controllers;

use App\Model\Procedencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ProcedenciaValidate;

class ProcedenciaController extends Controller
{
    /**
     * Visualiza todos los Procedenciaes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Procedencias=Procedencia::all();
        }else{
            $Procedencias=Procedencia::paginate(8);
        }
        return response()->json($Procedencias);
    }

    /**
     * Registra un nuevo Procedencia
     */
    public function store(ProcedenciaValidate $request)
    {
        $Procedencia=new Procedencia();
        $Procedencia->nom_procedencia=$request->nom_procedencia;
        $Procedencia->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Procedencia Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Procedencia
     */
    public function show($id)
    {
        $Procedencia=Procedencia::where('id',$id)->first();
        return response()->json($Procedencia);
    }
        
    public function update(ProcedenciaValidate $request, $id)
    {
        $Procedencia=Procedencia::where('id',$id)->first();
        $Procedencia->nom_procedencia=$request->nom_procedencia;     
        $Procedencia->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Procedencia Actualizada"
        ]);
    }
}
