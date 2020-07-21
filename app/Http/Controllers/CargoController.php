<?php

namespace App\Http\Controllers;

use App\Model\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\CargoValidate;

class CargoController extends Controller
{
    /**
     * Visualiza todos los Cargoes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Cargos=Cargo::all();
        }else{
            $Cargos=Cargo::paginate(8);
        }
        return response()->json($Cargos);
    }

    /**
     * Registra un nuevo Cargo
     */
    public function store(CargoValidate $request)
    {
        $Cargo=new Cargo();
        $Cargo->nom_cargo=strtoupper($request->nom_cargo);
        $Cargo->proceso_id=$request->proceso_id;
        $Cargo->area_id=$request->area_id;
        $Cargo->labor_id=$request->labor_id;
        $Cargo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Cargo Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Cargo
     */
    public function show($id)
    {
        $Cargo=Cargo::where('id',$id)->first();
        return response()->json($Cargo);
    }
        
    public function update(CargoValidate $request, $id)
    {
        $Cargo=Cargo::where('id',$id)->first();
        $Cargo->nom_cargo=strtoupper($request->nom_cargo);
        $Cargo->proceso_id=$request->proceso_id;
        $Cargo->area_id=$request->area_id;
        $Cargo->labor_id=$request->labor_id;
        $Cargo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Cargo Actualizada"
        ]);
    }
}
