<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Operador;
use App\Model\Turno;
use App\Model\Marcador;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            $operador->planilla_id=1;
            $operador->save();
        }
        $fecha_hoy=Carbon::parse($request->fecha)->format('Y-m-d');
        $marcador=Marcador::where('codigo_operador',$operador->dni)
            ->where(DB::raw("DATE(ingreso)"),$fecha_hoy)
            ->where("turno",$request->turno)
            ->first();
        // if ($marcador==null) {
        //     return response()->json([
        //         "status"    =>"WARNING",
        //         "data"  =>'Operador no marco Asistencia.'
        //     ]);
        // }
        
        $tareo_anterior=Tareo::where('fecha',$request->fecha)
                            ->where('turno_id',$request->turno)
                            ->where('codigo_operador',$operador->dni)
                            ->orderBy('id','DESC')
                            ->first();


        // dd($operador->dni);
        $tareo_existe=Tareo::where('codigo_operador',$operador->dni)
                ->where('linea_id',$request->linea_id)
                ->where('proceso_id',$request->proceso_id)
                ->where('labor_id',$request->labor_id)
                ->where('area_id',$request->area_id)
                ->where('fundo_id',$request->fundo_id)
                ->where('turno_id',$request->turno)
                ->where('fecha',$request->fecha)
                ->orderBy('id','DESC')
                ->first();
        // dd($tareo_existe,$tareo_anterior);
        if ($tareo_anterior!=null) {
            // dd($tareo_anterior,$request->all());
            // dd($request->all());
            if ($tareo_anterior->id==$tareo_existe->id) {
                return response()->json([
                    "status" => "warning",
                    "message" => "Ya fue tareado."
                ]);
            }
            $marca_a_cerrar = Marcador::where('tareo_id',$tareo_anterior->id)
                        ->whereNull('salida')
                        ->first();
            $marca_a_cerrar->salida=Carbon::now();
            $marca_a_cerrar->save();
            $marca_creada=new Marcador();
            $marca_creada->codigo_operador=$operador->dni;
            $marca_creada->ingreso=Carbon::now();
            $marca_creada->salida=null;
            $marca_creada->turno=$request->turno;
            $marca_creada->cuenta_id=$request->user_id;
            $marca_creada->fecha_ref=Carbon::now();
            $marca_creada->save();
        }        
        
        $tareo=new Tareo();
        $tareo->codigo_operador=$operador->dni;
        $tareo->linea_id=$request->linea_id;
        $tareo->proceso_id=$request->proceso_id;
        $tareo->labor_id=$request->labor_id;
        $tareo->area_id=$request->area_id;
        $tareo->fundo_id=$request->fundo_id;
        $tareo->cuenta_id=$request->user_id;
        $tareo->fecha=$fecha_hoy;
        $tareo->turno_id=$request->turno;
        $tareo->save();

        $marcas=Marcador::where('codigo_operador',$operador->dni)
            ->where('fecha_ref',$request->fecha)
            ->where("turno",$request->turno)
            ->whereNull('tareo_id')
            ->get();
        
        foreach ($marcas as $key => $marca) {
           $marca->tareo_id=$tareo->id;
           $marca->save();
        }

        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
    }
}
