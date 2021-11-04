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
        DB::beginTransaction();
        try {
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
            
            $tareo_anterior=Tareo::where('fecha',$request->fecha)
                                ->where('turno_id',$request->turno)
                                ->where('codigo_operador',$operador->dni)
                                ->orderBy('id','DESC')
                                ->first();

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
            if ($tareo_anterior!=null) {
                if ($tareo_existe!=null) {
                    if ($tareo_anterior->id==$tareo_existe->id) {
                        return response()->json([
                            "status" => "WARNING",
                            "data" => "Ya fue tareado."
                        ]);
                    }
                }
                /**
                 * En caso tenga una marca a cerrar, 
                 * se creara un tareo con la Hora ingreso de realizada la operacion
                 */
                $marca_a_cerrar = Marcador::where('tareo_id',$tareo_anterior->id)
                            ->whereNull('salida')
                            ->first();
                if ($marca_a_cerrar) {
                    $marca_a_cerrar->salida=Carbon::now();
                    $marca_a_cerrar->save();
                    $marca_creada=new Marcador();
                    $marca_creada->codigo_operador=$operador->dni;
                    $marca_creada->ingreso=Carbon::now();
                    $marca_creada->salida=null;
                    $marca_creada->turno=$request->turno;
                    $marca_creada->cuenta_id=$request->user_id;
                    $marca_creada->fecha_ref=$marca_a_cerrar->fecha_ref;
                    $marca_creada->save();
                }
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

            //Marcas anteriores, regulariza si se hizo un tareo post marcas
            $marcas=Marcador::where('codigo_operador',$operador->dni)
                ->where('fecha_ref',$request->fecha)
                ->where("turno",$request->turno)
                ->whereNull('tareo_id')
                ->get();
            
            foreach ($marcas as $key => $marca) {
            $marca->tareo_id=$tareo->id;
            $marca->save();
            }
            DB::commit();
            return response()->json([
                "status"=> "OK",
                "data"  => $operador
            ]);
        } catch (\Exception $ex) {
            DB::rollback();
        }
    }
}
