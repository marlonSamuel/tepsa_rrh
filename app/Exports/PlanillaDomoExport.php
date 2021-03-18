<?php

namespace App\Exports;

use App\Traits\HelperReporteStyle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PlanillaDomoExport implements FromCollection, WithEvents,  ShouldAutoSize,  WithCustomStartCell, WithColumnFormatting, WithTitle, WithStrictNullComparison, WithDrawings,WithHeadings
{

    use HelperReporteStyle;

    private $star_cell = 5;
    private $data;
    private $finish_header;
    private $title;
    private $heading;
    private $planilla;
    private $columns_to_filter;
    private $p_name;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($title,$heading,$data,$planilla,$columns_to_filter = 'AD',$p_name = 'IP')
    {
        $this->title = $title;
        $this->heading = $heading;
        $this->data = $data;
        $this->planilla = $planilla;
        $this->columns_to_filter = $columns_to_filter;
        $this->p_name = $p_name;
    }

    public function collection()
    {
         return $this->data;
    }

    public function headings() : array
    {
        return $this->heading;
    }

    public function startCell(): string
    {
        return 'A'.$this->star_cell;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('logo_tepsa_rrh');
        $drawing->setPath(public_path('img/logo.jpg'));
        $drawing->setHeight(45);
        $drawing->setCoordinates('I2');

        return $drawing;
    }

     public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $rows = count($this->data);

                $event->sheet->mergeCells('A2:F2');
                $event->sheet->setCellValue('A2', mb_strtoupper('DEPARTAMENTO DE OPERACIONES                                                                PLANILLA PERSONAL DOMO', 'UTF-8'));

                $event->sheet->mergeCells('A3:C3');
                $event->sheet->setCellValue('A3','PLANILLA NO: '.$this->planilla->numero);
                $event->sheet->mergeCells('A4:D4');
                $event->sheet->setCellValue('A4',
                                            'FECHA INICIO DESCARGA: '.date('d M Y', strtotime($this->planilla->inicio_descarga)).'          '.
                                            'FECHA FIN DESCARGA:    '.date('d M Y', strtotime($this->planilla->fin_descarga)));

                $event->sheet->setCellValue('G2','LUGAR:');
                $event->sheet->setCellValue('H2','MUELLE EPQ');

                $event->sheet->setCellValue('G3','BUQUE:');
                $event->sheet->setCellValue('H3',$this->planilla->buque);

                $styleHeadings = $this->getStyleToHead();
                $styleHeaderArray = $this->getStyleToTitle(); 

                $event->sheet->getStyle('A1:R4')->applyFromArray($styleHeaderArray);//title header

                $event->sheet->getStyle('A5:'.$this->columns_to_filter.'5')->applyFromArray($styleHeadings);
                $event->sheet->setAutoFilter('A5:'.$this->columns_to_filter.'5');

                $border = $this->getBorder();

                $event->sheet->getStyle('A6:'.$this->columns_to_filter.(string)(4+$rows))->applyFromArray($border);

                $event->sheet->getStyle('A'.(string)(5+$rows).':'.$this->columns_to_filter.(string)(5+$rows))->applyFromArray($this->getBold());
            },
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function columnFormats(): array
    {
        switch ($this->p_name) {
            case 'IP':
                return [
                    'H6:U'.count($this->data) => "Q 0.00"
                ];
                break;

            case 'MC':
                return [
                    'H6:I'.count($this->data) => "Q 0.00",
                    'K6:L'.count($this->data) => "Q 0.00",
                    'N6:O'.count($this->data) => "Q 0.00",
                    'W6:AJ'.count($this->data) => "Q 0.00"
                ];
                break;
            default:
                return [];
                break;
        }
    }

    //retornar footer
    public function footer()
    {
        $init = $this->star_cell+1;
        $c = $init;

        $data_sums = collect();

        $turnos_trabajados = $this->data->sum('turnos_trabajados');


        foreach ($data as $d) {

            $c = $c+count($m['data']);

            $cell_d = 'D'.($c);
            $cell_e = 'E'.($c);
            $cell_h = 'H'.($c);
            $c = $c+8;

            if($reporte=='lm'){
                $data_sums->push([
                    ['cell'=>$cell_d,'value'=>$tiempo_tarde_l],
                    ['cell'=>$cell_e,'value'=>$tiempo_extra_l],
                    ['cell'=>$cell_h,'value'=>$tiempo_tarde_a]
                ]);
            }else if($reporte == 'ea'){
                $data_sums->push([
                    ['cell'=>$cell_d,'value'=>$tiempo_tarde_a]
                ]);
            }else if($reporte == 'ti'){
                $data_sums->push([
                    ['cell'=>$cell_d,'value'=>$tiempo_tarde_l]
                ]);
            }else{
                $data_sums->push([
                    ['cell'=>$cell_d,'value'=>$tiempo_extra_l]
                ]);
            }
        }


        return $data_sums;
    }
}