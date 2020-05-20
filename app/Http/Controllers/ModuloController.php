<?php

namespace App\Http\Controllers;

use App\Model\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ModuloValidate;

class ModuloController extends Controller
{
    /**
     * Visualiza todos los Moduloes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Modulos=Modulo::all();
        }else{
            $Modulos=Modulo::paginate(8);
        }
        return response()->json($Modulos);
    }

    /**
     * Registra un nuevo Modulo
     */
    public function store(ModuloValidate $request)
    {
        $Modulo=new Modulo();
        $Modulo->nombre_modulo=$request->nombre_modulo;
        $Modulo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Modulo Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Modulo
     */
    public function show($id)
    {
        $Modulo=Modulo::where('id',$id)->first();
        return response()->json($Modulo);
    }
        
    public function update(ModuloValidate $request, $id)
    {
        $Modulo=Modulo::where('id',$id)->first();
        $Modulo->nombre_modulo=$request->nombre_modulo;
        $Modulo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Modulo Actualizada"
        ]);
    }
}
