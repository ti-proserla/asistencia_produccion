<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labor2;
use App\Model\Area;
use App\Model\Area2;
use App\Model\Labor;
use App\Model\Fundo;
use App\Model\Configuracion;
use App\Model\Consumidor;
use App\Model\Proceso;
use App\Model\Operador;
use App\Model\Planilla;
use App\Model\Tareo;
use App\Model\Marcador;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


class SincronizarController extends Controller
{
    public function labor(){
        $areas=Area::select('id')->get();
        $idAreas=[];
        foreach ($areas as $key => $area) {
            $idAreas[]=$area->id;
        }
        $laboresSqlServer=Labor2::selectRaw('RTRIM(DESCRIPCION) as nom_labor,IDACTIVIDAD as area_id,IDLABOR as id')
            ->whereIn('IDACTIVIDAD',$idAreas)
            ->get();
        foreach ($laboresSqlServer as $key => $value) {
            try {
                $labor=new Labor();
                $labor->id=str_pad($value->id, 6, "0", STR_PAD_LEFT);
                $labor->nom_labor=$value->nom_labor;
                $labor->area_id=$value->area_id;
                $labor->save();
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }
        return "FINALIZADO";
    }

    public function area(){
        $configuracion=Configuracion::select('actividad')->first();
        $parametros=explode(',',$configuracion->actividad);
        $areasSqlServer=Area2::selectRaw('RTRIM(DESCRIPCION) as nom_area,RTRIM(IDACTIVIDAD) as codigo')
            ->whereIn('IDACTIVIDAD',$parametros)
            ->get();
        foreach ($areasSqlServer as $key => $value) {
            try {
                $area=new Area();
                $area->id=str_pad($value->codigo, 3, "0", STR_PAD_LEFT);
                $area->nom_area=$value->nom_area;
                $area->save();        
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }
        return "FINALIZADO";
    }
    public function proceso(){
        $configuracion=Configuracion::select('ccosto')->first();
        $parametros=explode(',',$configuracion->ccosto);
        $consumidores_fundos=Consumidor::whereIn('IDCONSUMIDOR',$parametros)
                                ->selectRaw('RTRIM(IDCONSUMIDOR) idconsumidor,RTRIM(DESCRIPCION) nom_fundo')
                                ->get();
        // dd($parametros,$consumidores_fundos);
        // foreach ($consumidores_fundos as $key => $value) {
        //     try {
        //         $fundo=new Fundo();
        //         $fundo->id=$value->idconsumidor;
        //         $fundo->nom_fundo=$value->nom_fundo;
        //         $fundo->save();        
        //         echo "Guardado<br>";
        //     } catch (\Exception $ex) {
        //         echo "Existente<br>";
        //     }
        // }

        $consumidores=Consumidor::selectRaw('RTRIM(CONSUMIDOR.IDCONSUMIDOR) as idconsumidor, LTRIM(CONSUMIDOR.DESCRIPCION) as nom_proceso,RTRIM(C2.IDCONSUMIDOR) as fundo_id')
            ->join('CONSUMIDOR as C2','C2.JERARQUIA','=',DB::raw('LEFT(CONSUMIDOR.JERARQUIA,(LEN(CONSUMIDOR.JERARQUIA)-CASE WHEN LEN(CONSUMIDOR.JERARQUIA)=3 THEN 3 ELSE 4 END))'))
            ->whereIn('C2.IDCONSUMIDOR',$parametros)
            ->get();
        foreach ($consumidores as $key => $value) {
            try {
                $proceso=new Proceso();
                $proceso->id=$value->idconsumidor;
                $proceso->nom_proceso=$value->nom_proceso;
                $proceso->fundo_id=$value->fundo_id;
                $proceso->save();        
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }
        return "FINALIZADO";
    }

    public function tareo(Request $request){
        $rowids=[];
        for ($i=0; $i < count($request->data) ; $i++) { 
            $row=$request->data[$i];
            
            $tareo=new Tareo();
            $tareo->codigo_operador=$row['codigo_operador'];
            $tareo->proceso_id=$row['proceso_id'];
            $tareo->labor_id=$row['labor_id'];
            $tareo->area_id=$row['area_id'];
            $tareo->fundo_id=$row['fundo_id'];
            $tareo->cuenta_id=$row['cuenta_id'];
            $tareo->fecha=$row['fecha'];
            $tareo->save();
            array_push($rowids,$row['rowid']);
        }
        return response()->json([
            "status"    => "OK",
            "data"      => $rowids
        ]);
    }

    public function asistencia(Request $request){
        $asistencia=Marcador::where('fecha_ref',$request->fecha)
                    ->where('fundo_id',$request->fundo_id)
                    ->join('operador','operador.dni','=','marcador.codigo_operador')
                    ->select('codigo_operador','fecha_ref','nom_operador','fundo_id')
                    ->groupBy('codigo_operador','fecha_ref','nom_operador','fundo_id')
                    ->get();
        return response()->json($asistencia);
    }

    //Sync Imput
    public function marcas(Request $request){
        $data=$request->data;
        for ($i=0; $i < count($data); $i++) {
            $marca=$data[$i];
            $codigo=$marca['codigo_operador'];
            $fecha=$marca['fecha'];
            $fundo_id=$marca['fundo_id'];
            $operador=Operador::where('dni',$codigo)->first();
            if ($operador==null) {
                $operador=new Operador();
                $operador->dni=$codigo;
                $operador->nom_operador="Nuevo";
                $operador->ape_operador="Trabajador";
                $operador->planilla_id=1;
                $operador->save();
            }

            $planilla_id=0;
            if ($operador->planilla_id==null) {
                $planilla_id=1;
            }else {
                $planilla_id=$operador->planilla_id;
            }
            $salida=Planilla::where('id',$planilla_id)->first()->salida;
            $fecha_analisis=Carbon::parse($fecha);
            $fecha_limite=Carbon::parse($fecha)->startOfDay()->addHours($salida);
            $fecha_consulta=($fecha_analisis<$fecha_limite) ? Carbon::parse($fecha)->subDay()->format('Y-m-d') : Carbon::parse($fecha)->format('Y-m-d');
            // dd($fecha_consulta);

            $consulta_1=Marcador::where('codigo_operador',$codigo)
                        ->where('fecha_ref','>=',$fecha_consulta)
                        ->where('fecha_ref','<=',$fecha_analisis)
                        ->select('codigo_operador','fecha_ref',DB::raw('min(ingreso) ingreso,MAX(id) id'))
                        ->having('ingreso','>',DB::raw('DATE_SUB("'.Carbon::now().'", INTERVAL 16 HOUR)'))
                        ->groupBy('codigo_operador','fecha_ref')
                        ->first();
            // dd($marca,$operador,$consulta_1);
            $marcador=null;
        
            if ($consulta_1!=null) {
                $marcador=Marcador::where('id',$consulta_1->id)->first();
            }

            do {
                if ($marcador!=null) { // En caso sea su primera marca en todo el sistema.
                    $tiempo_entre_marcas=Planilla::where('id',$planilla_id)->first()->tiempo_entre_marcas;
                    $fecha_limite=Carbon::parse($fecha)->subMinute($tiempo_entre_marcas);
                    
                    if(
                        ( $marcador->salida == null && $fecha_limite < Carbon::parse($marcador->ingreso) ) ||
                        ( $marcador->salida != null && $fecha_limite < Carbon::parse($marcador->salida) )
                    ) {
                        $min=0;
                        if ($marcador->salida == null) {
                            $min=Carbon::parse($marcador->ingreso)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                        }else {
                            $min=Carbon::parse($marcador->salida)->addMinutes($tiempo_entre_marcas+1)->format('H:i');
                        }
                        //salir por horas continuas
                        break 1;
                    }
                }

                if (is_null($marcador)) {
                    $marcador=new Marcador();
                    $marcador->codigo_operador=$codigo;
                    $marcador->ingreso=$fecha_analisis;
                    $marcador->salida=null;
                    $marcador->fundo_id=$fundo_id;
                    $marcador->fecha_ref=$fecha_analisis;
                    $marcador->save();
                }else{
                    if (!is_null($marcador->salida)) {
                        $newMarcador=$marcador;
                        $marcador=new Marcador();
                        $marcador->codigo_operador=$codigo;
                        $marcador->ingreso=$fecha_analisis;
                        $marcador->salida=null;
                        $marcador->fundo_id=$fundo_id;
                        $marcador->fecha_ref=$newMarcador->fecha_ref;
                        $marcador->save();
                    }else{
                        $marcador->salida=$fecha_analisis;
                        $marcador->save();
                    }
                    
                }
            } while (0);

        }
        return response()->json([
            "status"    => "OK"
        ]);
    }
}
