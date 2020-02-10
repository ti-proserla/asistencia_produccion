<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Configuracion;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Configuracion::first());
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->has('ccosto') ? $request->ccosto : $configuracion->ccosto);
        $configuracion=Configuracion::first();
        $configuracion->tiempo_entre_marcas=$request->has('tiempo_entre_marcas') ? $request->tiempo_entre_marcas : $configuracion->tiempo_entre_marcas;
        // $configuracion->hora_cierre_turno=$request->has('hora_cierre_turno') ? $request->hora_cierre_turno : $configuracion->hora_cierre_turno;
        $configuracion->actividad=$request->has('actividad') ? $request->actividad : $configuracion->actividad;
        $configuracion->ccosto=$request->has('ccosto') ? $request->ccosto : $configuracion->ccosto;
        $configuracion->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Parametros Actualizados"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
