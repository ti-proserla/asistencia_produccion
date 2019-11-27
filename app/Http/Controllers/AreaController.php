<?php

namespace App\Http\Controllers;

use App\Model\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\AreaValidate;

class AreaController extends Controller
{
    /**
     * Visualiza todos los Areaes
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Areas=Area::all();
        }else{
            $Areas=Area::paginate(8);
        }
        return response()->json($Areas);
    }
    /**
     * Area Labor , muestra un JSON de Areas con un sub-Json de sus Labores 
     */
    public function labor(Request $request){
        $areas=Area::with('labores')->get();
        return response()->json($areas);
    }
    /**
     * Registra un nuevo Area
     */
    public function store(AreaValidate $request)
    {
        $Area=new Area();
        $Area->codigo=$request->codigo;
        $Area->nom_area=$request->nom_area;
        $Area->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Area Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Area
     */
    public function show($id)
    {
        $Area=Area::where('id',$id)->first();
        return response()->json($Area);
    }
        
    public function update(AreaValidate $request, $id)
    {
        $Area=Area::where('id',$id)->first();
        $Area->codigo=$request->codigo;
        $Area->nom_area=$request->nom_area;      
        $Area->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Area Actualizada"
        ]);
    }

    /**
     * Cambiar de estado a la Area
     */
    public function estado($id)
    {
        $Area=Area::where('id',$id)->first();
        $Area->estado=($Area->estado=='0') ? '1' : '0';
        $Area->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Estado Actualizado"
        ]);
    }
}
