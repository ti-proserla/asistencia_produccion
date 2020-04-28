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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// class HorasSemanaTrabajadorExport implements FromCollection
class HorasSemanaTrabajadorExport implements FromView, WithColumnFormatting, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $resultado;
 
    public function __construct($resultado)
    {
        $this->resultado=$resultado;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        return view('excel.horastrabajador', [
            'operadores' => $this->resultado
        ]);
    }
}
