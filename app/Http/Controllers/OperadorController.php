<?php

namespace App\Http\Controllers;

use App\Model\Operador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\NuevoOperador;
use App\Http\Requests\OperadorEditar;

class OperadorController extends Controller
{
    /**
     * Visualiza todos los Operadores
     */
    public function index()
    {
        $operadores=Operador::paginate(8);
        return response()->json($operadores);
    }

    /**
     * Registra un nuevo Operador
     */
    public function store(NuevoOperador $request)
    {
        $operador=new Operador();
        $operador->dni=$request->dni;
        $operador->nom_operador=strtoupper($request->nom_operador);
        $operador->ape_operador=strtoupper($request->ape_operador);
        $operador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Operador Registrado"
        ]);
    }
    
    /**
     * Visualiza datos de un solo operador
     */
    public function show($id)
    {
        $operador=Operador::where('id',$id)->first();
        return response()->json($operador);
    }
        
    public function update(OperadorEditar $request, $id)
    {
        $operador=Operador::where('id',$id)->first();
        $operador->dni=$request->dni;
        $operador->nom_operador=strtoupper($request->nom_operador);        
        $operador->ape_operador=strtoupper($request->ape_operador);        
        $operador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Operador Actualizado"
        ]);
    }

    /**
     * Desabilita al operador
     */
    public function estado($id)
    {
        $operador=Operador::where('id',$id)->first();
        $operador->estado=($operador->estado=='0') ? '1' : '0';
        $operador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Estado Actualizado"
        ]);
    }
}
