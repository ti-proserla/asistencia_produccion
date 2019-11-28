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
class MarcasTurnoTrabajadorExport implements FromView, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $turno_id;
 
    public function __construct(int $turno_id)
    {
        $this->turno_id = $turno_id;
    }

    public function columnFormats(): array
    {
        return [
            // 'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        $resultado=$resultado=Operador::join('marcador','operador.id','=','marcador.operador_id')
            ->select('dni','nom_operador','ape_operador',DB::raw('GROUP_CONCAT(CONCAT_WS("@",marcador.ingreso,marcador.salida) ORDER BY marcador.ingreso ASC SEPARATOR "@") AS marcas'),DB::raw('ROUND(SUM(TIMESTAMPDIFF(MINUTE,marcador.ingreso,marcador.salida)/60 ),2) AS total'))
            ->where('marcador.turno_id',$this->turno_id)
            ->groupBy('operador.dni')
            ->get();
        return view('excel.marcastrabajador', [
            'operadores' => $resultado
        ]);
    }
}
