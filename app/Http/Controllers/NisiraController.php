<?php

namespace App\Http\Controllers;

use App\Model\NPeriodo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NisiraController extends Controller
{
    public function periodo(){

        $periodo_semana=NPeriodo::where('SEMANA','<>','')->where('IDPLANILLA','=','OBR')->where('ANIO','>',2019)
            ->select(DB::raw("ANIO anio, PERIODO periodo, SEMANA semana, FORMAT(FECHA_INI,'yyyy-MM-dd') inicio, FORMAT(FECHA_FIN,'yyyy-MM-dd') fin"))
            ->get();
        return response()->json($periodo_semana);
    }
}
