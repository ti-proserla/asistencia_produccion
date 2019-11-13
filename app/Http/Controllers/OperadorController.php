<?php

namespace App\Http\Controllers;

use App\Model\Operador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\NuevoOperador;

class OperadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operadores=Operador::all();
        return response()->json($operadores);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoOperador $request)
    {
        $operador=new Operador();
        $operador->dni=$request->dni;
        $operador->nom_operador=$request->nom_operador;
        $operador->ape_operador=$request->ape_operador;
        $operador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Operador Registrado"
        ]);
        // dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Operador  $operador
     * @return \Illuminate\Http\Response
     */
    public function show($dni)
    {
        $operador=Operador::where('dni',$dni)->first();
        return response()->json($operador);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Operador  $operador
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Operador  $operador
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $dni)
    {
        $operador=Operador::where('dni',$dni)->first();
        $operador->nom_operador=$request->nom_operador;        
        $operador->ape_operador=$request->ape_operador;        
        $operador->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Operador Actualizado"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Operador  $operador
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
