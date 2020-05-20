<?php

namespace App\Http\Controllers;

use App\Model\Rol;
use App\Model\RolModulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\RolValidate;

class RolController extends Controller
{
    /**
     * Visualiza todos los Roles
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Rols=Rol::all();
        }else{
            $Rols=Rol::paginate(8);
        }
        return response()->json($Rols);
    }

    /**
     * Registra un nuevo Rol
     */
    public function store(RolValidate $request)
    {
        $Rol=new Rol();
        $Rol->nombre_rol=$request->nombre_rol;
        $Rol->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Rol Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Rol
     */
    public function show($id)
    {
        $Rol=Rol::where('id',$id)->first();
        return response()->json($Rol);
    }
        
    public function update(RolValidate $request, $id)
    {
        $Rol=Rol::where('id',$id)->first();
        $Rol->nombre_rol=$request->nombre_rol;
        $Rol->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Rol Actualizada"
        ]);
    }

    public function showModulos(Request $request,$id){
        $arrayModulos=[];
        $modulos=RolModulo::join('modulo','modulo.id','=','rol_modulo.modulo_id')
            ->where('rol_modulo.rol_id',$id)
            ->select('rol_modulo.modulo_id','nombre_modulo')
            ->get();
        if ($request->has('name')) {
            foreach ($modulos as $key => $modulo) {
                array_push($arrayModulos,$modulo->nombre_modulo);
            }
        }else{
            foreach ($modulos as $key => $modulo) {
                array_push($arrayModulos,$modulo->modulo_id);
            }
        }
        
        return $arrayModulos;
    }

    public function updateModulos(Request $request,$id){
        try {
            $res=DB::select(DB::raw('DELETE FROM rol_modulo where rol_id=:rol_id'),[
                'rol_id' => $id
            ]);
            // dd($res);
            for ($i=0; $i < count($request->modulos) ; $i++) { 
                $modulo_id=$request->modulos[$i];
                $rolModulo = new RolModulo();
                $rolModulo->rol_id=$id;
                $rolModulo->modulo_id=$modulo_id;
                $rolModulo->save();
            }
            return response()->json([
                "status"=> "OK",
                "data"  => "Modulos por Rol Guardados"
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "status"=> "ERROR",
                "data"  => $th->getMessage()
            ]);
        }
    }
}
