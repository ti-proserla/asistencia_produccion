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
class HorasRangoExport implements FromView, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;
 
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function columnFormats(): array
    {
        return [
            // 'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        
        return view('excel.marcasrango', [
            'operadores' => $this->data
        ]);
    }
}
