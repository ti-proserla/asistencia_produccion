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
        foreach ($consumidores_fundos as $key => $value) {
            try {
                $fundo=new Fundo();
                $fundo->id=$value->idconsumidor;
                $fundo->nom_fundo=$value->nom_fundo;
                $fundo->save();        
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }

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
}
