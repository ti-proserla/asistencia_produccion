<?php

namespace App\Http\Controllers;

use App\Model\Linea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\LineaValidate;

class LineaController extends Controller
{
    /**
     * Visualiza todos los Lineaes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Lineas=Linea::all();
        }else{
            $Lineas=Linea::paginate(8);
        }
        return response()->json($Lineas);
    }

    /**
     * Registra un nuevo Linea
     */
    public function store(LineaValidate $request)
    {
        $Linea=new Linea();
        $Linea->nombre=$request->nombre;
        $Linea->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Linea Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Linea
     */
    public function show($id)
    {
        $Linea=Linea::where('id',$id)->first();
        return response()->json($Linea);
    }
        
    public function update(LineaValidate $request, $id)
    {
        $Linea=Linea::where('id',$id)->first();
        $Linea->nombre=$request->nombre;      
        $Linea->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Linea Actualizada"
        ]);
    }
}
