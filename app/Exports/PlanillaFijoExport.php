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



class PlanillaFijoExport implements FromCollection, WithEvents, ShouldAutoSize, WithCustomStartCell, WithTitle, WithStrictNullComparison, WithDrawings,WithHeadings
{
    use HelperReporteStyle;

	private $star_cell = 10;
	private $data;
	private $finish_header;
	private $title;
	private $heading;
	private $planilla;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($title,$heading,$data,$planilla)
    {
    	$this->title = $title;
    	$this->heading = $heading;
        $this->data = $data;
        $this->planilla = $planilla;
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
        $drawing->setHeight(60);
        $drawing->setCoordinates('A2');

        return $drawing;
    }

     public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->mergeCells('C2:I4');
                $event->sheet->setCellValue('C2', mb_strtoupper('TERMINAL ESPECIALIZADAS DEL PACIFICO, S.A', 'UTF-8'));

                $event->sheet->mergeCells('A5:B5');
            	$event->sheet->setCellValue('A5','DEPARTAMENTO DE OPERACIONES');

            	//$event->sheet->mergeCells('B6:C6');
            	$event->sheet->setCellValue('A6','PLANILLA NO:');
            	$event->sheet->setCellValue('B6',$this->planilla->quincena.'-'.$this->planilla->anio->anio);

            	$event->sheet->setCellValue('C6','FECHA PLANILLA:');
            	$event->sheet->setCellValue('D6',date('d M Y', strtotime($this->planilla->fecha_fin)));

            	//$event->sheet->mergeCells('B7:C7');
            	$event->sheet->setCellValue('A7','PERIODO DEL: ');
            	$event->sheet->setCellValue('B7',date('d M Y', strtotime($this->planilla->fecha_inicio)));

            	//$event->sheet->mergeCells('E7:F7');
            	$event->sheet->setCellValue('C7','AL: ');
            	$event->sheet->setCellValue('D7',date('d M Y', strtotime($this->planilla->fecha_fin)));


            	$styleHeadings = $this->getStyleToHead();
                $styleHeaderArray = $this->getStyleToTitle(); 

                $event->sheet->getStyle('A1:R8')->applyFromArray($styleHeaderArray);//title header

                $event->sheet->getStyle('A10:AD10')->applyFromArray($styleHeadings);
                $event->sheet->setAutoFilter('A10:AD10');

                //$event->sheet->getStyle('A11:B11')->applyFromArray($styleArray); //setear rango de estilos para header
            },
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}