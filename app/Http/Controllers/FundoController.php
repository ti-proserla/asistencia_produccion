<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Fundo;
use App\Model\Cuenta;

class FundoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('usuario')) {
            $cuenta=Cuenta::where('usuario',$request->usuario)->first();
            if ($cuenta!=null) {
                if ($cuenta->rol=="COMUN") {
                    $fundos=Fundo::select('fundo.id','fundo.nom_fundo')->join('privilegios','privilegios.fundo_id','=','fundo.id')
                    ->where('cuenta_id',$cuenta->id)
                    ->get();
                }
                if('ADMINISTRADOR'==$cuenta->rol) {
                    $fundos=Fundo::all();
                }
                return response()->json($fundos);
            }else {
                return response()->json([]);
            }
        }
        $fundos=Fundo::select('nom_fundo','id');
        if ($request->all==true) {
            $fundos=$fundos->get();
        }else{
            $fundos=$fundos->paginate(8);
        }
        return response()->json($fundos);
    }

    public function proceso(Request $request){
        // dd("hola");
        $procesos_fundo=Fundo::with('procesos')
            ->get();
        
        return response()->json($procesos_fundo);
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
        //
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
        //
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
