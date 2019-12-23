<?php

namespace App\Http\Controllers;

use App\Model\Operador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};

use App\Http\Requests\NuevoOperador;
use App\Http\Requests\OperadorEditar;

class OperadorController extends Controller
{
    /**
     * Visualiza todos los Operadores
     */
    public function index(Request $request)
    {
        if ($request->search==null||$request->search=="null") {
            $request->search="";
        }
        if ($request->all==true) {
            $operadores=Operador::where(DB::raw('CONCAT(nom_operador,ape_operador)'),'like','%'.$request->search.'%')->get();       
            return response()->json($operadores); 
        }
        $operadores=Operador::where(DB::raw('CONCAT(nom_operador,ape_operador)'),'like','%'.$request->search.'%')->paginate(15);
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
        $operador->planilla_id=$request->planilla_id;
        $operador->save();
        if($request->file('foto')!=null){
            $foto = $request->file('foto');
            $fileName = $operador->dni . '.jpeg';
            \Image::make($foto)
                ->save(public_path('/storage/operador/'.$fileName));
            $operador->foto=$fileName;
            $operador->save();
        }
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
        dd(utf8_encode($_REQUEST['ape_operador']));
        dd($request->all());
        $operador=Operador::where('id',$id)->first();
        $operador->nom_operador=strtoupper($request->nom_operador);        
        $operador->ape_operador=strtoupper($request->ape_operador);
        $operador->planilla_id=$request->planilla_id;
        $operador->save();
        if($request->file('foto')!=null){
            $conteo=glob(public_path('storage/operador/'.$operador->dni.'*'));
            $n=count($conteo);
            $foto = $request->file('foto');
            $fileName = $operador->dni.$n.'.jpeg';
            \Image::make($foto)
                ->save(public_path('/storage/operador/'.$fileName));
            $operador->foto=$fileName;
            $operador->save();
        }
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

    public function jne(Request $request,$dni){
        if ($request->all!=true) {
            $operador=Operador::where('dni',$dni)->first();
            if ($operador!=null) {
                $id=$operador->id;
                return json_encode([
                    "status" => "INFO",
                    "data"   => "El Trabajador ya se encuentra registrado",
                    "id"     => $id
                ]); 
            }
        }
        $cs = new Dni(new ContextClient(), new DniParser());
        $person = $cs->get($dni);
        if (!$person) {
            return json_encode([
                "status" => "ERROR",
                "data"   => "No encontrado"
            ]);
            // echo 'Not found';
            exit();
        }
        return json_encode([
                "status" => "OK",
                "data"   =>$person
            ]);
    }
}
