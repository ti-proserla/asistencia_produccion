<?php

namespace App\Http\Controllers;

use App\Model\Proceso;
use App\Model\Consumidor;
use App\Model\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ProcesoValidate;

class ProcesoController extends Controller
{
    /**
     * Visualiza todos los Procesoes
     */
    public function index(Request $request)
    {
        // dd($request->all());
        if ($request->all==true) {
            $Procesos=Proceso::where('fundo_id',$request->fundo_id)->all();
        }else{
            $Procesos=Proceso::where('fundo_id',$request->fundo_id)->paginate(8);
        }
        return response()->json($Procesos);
    }

    /**
     * Registra un nuevo Proceso
     */
    public function store(ProcesoValidate $request)
    {
        $Proceso=new Proceso();
        $Proceso->codigo=$request->codigo;
        $Proceso->nom_proceso=$request->nom_proceso;
        $Proceso->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Proceso Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Proceso
     */
    public function show($id)
    {
        $Proceso=Proceso::where('id',$id)->first();
        return response()->json($Proceso);
    }
        
    public function update(ProcesoValidate $request, $id)
    {
        $Proceso=Proceso::where('id',$id)->first();
        $Proceso->codigo=$request->codigo;
        $Proceso->nom_proceso=$request->nom_proceso;      
        $Proceso->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Proceso Actualizada"
        ]);
    }

    /**
     * Cambiar de estado a la Proceso
     */
    public function estado($id)
    {
        $Proceso=Proceso::where('id',$id)->first();
        $Proceso->estado=($Proceso->estado=='0') ? '1' : '0';
        $Proceso->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Estado Actualizado"
        ]);
    }
}
