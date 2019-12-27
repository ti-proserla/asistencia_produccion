<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Operador;
use App\Model\Turno;
use App\Model\Marcador;
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
        // dd($request->all());
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->save();
        }
        $marcador=Marcador::where('operador_id',$operador->id)
            ->where('turno_id',$request->turno_id)
            ->first();
        if ($marcador==null) {
            $turno=Turno::where('id',$request->turno_id)->first();
            $marcador=Marcador::where('operador_id',$operador->id)
                ->where('turno_id',$request->turno_id)
                ->first();
            return response()->json([
                "status"    =>"WARNING",
                "data"  =>'Operador no marco Asistencia.'
            ]);
        }
        
        $tareo=new Tareo();
        $tareo->turno_id=$request->turno_id;
        $tareo->operador_id=$operador->id;
        $tareo->proceso_id=$request->proceso_id;
        $tareo->labor_id=$request->labor_id;
        $tareo->area_id=$request->area_id;
        $tareo->linea_id=$request->linea_id;
        $tareo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
    }
}
