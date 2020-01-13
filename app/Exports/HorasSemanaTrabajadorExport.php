<?php

namespace App\Exports;

use App\Model\Operador;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// class HorasSemanaTrabajadorExport implements FromCollection
class HorasSemanaTrabajadorExport implements FromView, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $week;
    private $year;
    private $planilla_id;
 
    public function __construct(int $year,int $week,int $planilla_id)
    {
        $this->week = $week;
        $this->year = $year;
        $this->planilla_id = $planilla_id;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        $resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->leftJoin(DB::raw('(SELECT * FROM tareo GROUP BY operador_id,turno_id) AS T'),function($join){
                $join->on('T.operador_id', '=', 'marcador.operador_id');
                $join->on('T.turno_id', '=', 'marcador.turno_id');
            })
            ->leftJoin('labor','labor.id','=','T.labor_id')
            ->leftJoin('area','area.id','=','labor.area_id')
            ->leftJoin('proceso','proceso.id','=','T.proceso_id')
            ->select(DB::raw("operador.dni, CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido, CONCAT(DATE_FORMAT(ingreso, '%Y%m'),'-',WEEK(ingreso,3)) AS periodo, T.area_id As codActividad, T.labor_id AS codLabor, T.proceso_id As codProceso, labor.nom_labor,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Lunes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Martes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Miercoles, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Jueves, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Viernes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Sabado,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Domingo"))
            ->groupBy('dni','procesos.operador.nom_operador','procesos.operador.ape_operador',DB::raw('DATE(ingreso)'))
            ->where(DB::raw('WEEK(ingreso,3)'),$this->week)
            ->where(DB::raw('YEAR(ingreso)'),$this->year);
            if ($this->planilla_id==null||$this->planilla_id==""||$this->planilla_id==0) {
                $resultado=$resultado->whereNull('operador.planilla_id');
            }else{
                $resultado=$resultado->where('planilla_id',$this->planilla_id);
            }
            $resultado=$resultado->get();
        return view('excel.horastrabajador', [
            'operadores' => $resultado
        ]);
    }
}
