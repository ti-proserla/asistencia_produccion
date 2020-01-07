<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Labor2;
use App\Model\Area;
use App\Model\Area2;
use App\Model\Labor;

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
        
        $areasSqlServer=Area2::select('DESCRIPCION as nom_area','IDACTIVIDAD as codigo')
            ->where('IDACTIVIDAD','010')
            ->orWhere('IDACTIVIDAD','011')
            ->orWhere('IDACTIVIDAD','012')
            ->orWhere('IDACTIVIDAD','009')
            ->get();
        
        foreach ($areasSqlServer as $key => $value) {
            try {
                $area=new Area();
                $area->id=str_pad($value->codigo, 3, "0", STR_PAD_LEFT);
                // $area->id=$value->codigo;
                $area->nom_area=$value->nom_area;
                $area->save();        
                echo "Guardado<br>";
            } catch (\Exception $ex) {
                echo "Existente<br>";
            }
        }
        return "FINALIZADO";
    }
}
