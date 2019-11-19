<?php

namespace App\Exports;

use App\Model\Operador;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

// class HorasSemanaTrabajadorExport implements FromCollection
class HorasSemanaTrabajadorExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $week;
    private $year;
 
    public function __construct(int $year,int $week)
    {
        $this->week = $week;
        $this->year = $year;
    }

    public function view(): View
    {
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->leftJoin(DB::raw('(SELECT * FROM tareo WHERE WEEK(tareo.created_at,3)='.$this->week.' AND YEAR(tareo.created_at)='.$this->year.') AS T'),'T.operador_id','=','operador.id')
            ->leftJoin('labor','labor.id','=','T.labor_id')
            ->leftJoin('area','area.id','=','labor.area_id')
            ->leftJoin('proceso','proceso.id','=','T.proceso_id')
            ->select(DB::raw("operador.dni As codigo, CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido, CONCAT(YEAR(ingreso),MONTH(ingreso),'-',WEEK(ingreso,3)) AS periodo, area.codigo As codActividad, labor.codigo AS codLabor, proceso.codigo As codProceso, labor.nom_labor,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Lunes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Martes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Miercoles, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Jueves, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Viernes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Sabado,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Domingo"))
            ->groupBy('codigo','procesos.operador.nom_operador','procesos.operador.ape_operador')
            ->where(DB::raw('WEEK(ingreso,3)'),$this->week)
            ->where(DB::raw('YEAR(ingreso)'),$this->year)
            ->get();
        return view('excel.horastrabajador', [
            'operadores' => $resultado
        ]);
    }
}
