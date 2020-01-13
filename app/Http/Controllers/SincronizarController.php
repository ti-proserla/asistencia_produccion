<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labor2;
use App\Model\Area;
use App\Model\Area2;
use App\Model\Labor;
use App\Model\Configuracion;
use App\Model\Consumidor;
use App\Model\Proceso;
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
        $configuracion=Configuracion::where('nombre','actividad')->first();
        $parametros=explode(',',$configuracion->parametro);
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
        $configuracion=Configuracion::where('nombre','ccosto')->first();
        $parametros=explode(',',$configuracion->parametro);
        $consumidores=Consumidor::selectRaw('RTRIM(IDCONSUMIDOR) as idconsumidor,RTRIM(DESCRIPCION) as nom_proceso')
            ->whereIn('IDCONSUMIDOR',$parametros)
            ->get();
        foreach ($consumidores as $key => $value) {
            try {
                $proceso=new Proceso();
                $proceso->id=$value->idconsumidor;
                $proceso->nom_proceso=$value->nom_proceso;
                $proceso->save();        
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }
        return "FINALIZADO";
    }
}
