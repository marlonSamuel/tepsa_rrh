<?php

namespace App\Exports;

use App\Exports\PlanillaEventualExport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class PlanillaEventualSheetsExport implements WithMultipleSheets
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
        	$sheets[] = new PlanillaEventualExport($key,$d[0],$d[1],$d[2],$d[3],$d[4]);
        }

        return $sheets;
    }
}
