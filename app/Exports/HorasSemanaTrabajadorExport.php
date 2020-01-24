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
    private $fundo_id;
 
    public function __construct(int $year,int $week,int $planilla_id,$fundo_id )
    {
        $this->week = $week;
        $this->year = $year;
        $this->planilla_id = $planilla_id;
        $this->fundo_id = $fundo_id;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        // dd($this->fundo_id,$this->planilla_id);
        $resultado=Operador::join('marcador','operador.dni','=','marcador.codigo_operador')
            ->leftJoin(DB::raw('(SELECT * FROM tareo GROUP BY codigo_operador,DATE(tareo.fecha)) AS T'),function($join){
                $join->on('T.codigo_operador', '=', 'marcador.codigo_operador');
                $join->on(DB::raw("DATE(T.fecha)"), '=',DB::raw("DATE(marcador.ingreso)"));
            })
            ->leftJoin('labor','labor.id','=','T.labor_id')
            ->selectRaw(
                "operador.dni,".
                "marcador.fundo_id,".
                "CONCAT(operador.nom_operador,' ',operador.ape_operador) NombreApellido,".
                "CONCAT(DATE_FORMAT(ingreso, '%Y%m'),'-',WEEK(ingreso,3)) periodo,".
                "T.area_id codActividad,".
                "T.labor_id codLabor,".
                "T.proceso_id codProceso,".
                "labor.nom_labor,".
                "ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Lunes,".
                " ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Martes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Miercoles, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Jueves, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Viernes, ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Sabado,ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60) ELSE 0 END),2) as Domingo"
            )
            ->groupBy('dni','operador.nom_operador','operador.ape_operador',DB::raw('DATE(ingreso)'))
            ->where(DB::raw('WEEK(ingreso,3)'),$this->week)
            ->where(DB::raw('YEAR(ingreso)'),$this->year)
            ->where('marcador.fundo_id',$this->fundo_id);
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
