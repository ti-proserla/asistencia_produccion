<?php

namespace App\Exports;

use App\Model\Operador;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Maatwebsite\Excel\Sheet;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

// class HorasSemanaTrabajadorExport implements FromCollection
class MarcasTurnoExport implements FromView, WithColumnFormatting, 
// ShouldAutoSize, 
WithDrawings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $resultado;
    private $fecha;
 
    public function __construct($resultado,$fecha)
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) { $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style); });
        // Sheet::macro('setWidth', function (Sheet $sheet, string $cellRange, array $style) { $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style); });

        $this->resultado=$resultado;
        $this->fecha=$fecha;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->styleCells(
                    'A5:H'.(count($this->resultado)+5),
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '00000000'],
                            ],
                        ]
                        ]
                    );
                    $event->sheet->styleCells(
                    'A5:H5',
                    [
                        'font' => [
                            'bold' => true,
                        ]
                    ]
                );
                $event->sheet->getColumnDimension('A')->setWidth(16);
                $event->sheet->getColumnDimension('B')->setWidth(18);
                $event->sheet->getColumnDimension('C')->setWidth(18);
                $event->sheet->getColumnDimension('D')->setWidth(18);
                $event->sheet->getColumnDimension('E')->setWidth(18);
                $event->sheet->getColumnDimension('F')->setWidth(18);
                $event->sheet->getColumnDimension('G')->setWidth(18);
                $event->sheet->getColumnDimension('H')->setWidth(10);
                // $event->sheet->setWidth('A', 200);
            },
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/storage/logotipo.png'));
        $drawing->setWidth(95);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT
        ];
    }

    

    public function view(): View
    {
        return view('excel.general.marcasturno', [
            'operadores'    => $this->resultado,
            'fecha'         => $this->fecha
        ]);
    }
}
