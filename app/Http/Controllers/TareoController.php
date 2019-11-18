<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Operador;
use Illuminate\Http\Request;

class TareoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
        $tareo=new Tareo();
        $tareo->operador_id=$operador->id;
        $tareo->proceso_id=$request->proceso_id;
        $tareo->labor_id=$request->labor_id;
        $tareo->area_id=$request->area_id;
        $tareo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
    }
}
