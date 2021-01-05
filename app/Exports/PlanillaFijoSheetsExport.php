<?php

namespace App\Exports;

use App\Exports\PlanillaFijoExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class PlanillaFijoSheetsExport implements WithMultipleSheets
{
   protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->data as $key => $d) {
        	$sheets[] = new PlanillaFijoExport($key,$d[0],$d[1],$d[2]);
        }

        return $sheets;
    }
}