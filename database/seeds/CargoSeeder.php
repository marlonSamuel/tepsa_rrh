<?php

use App\Imports\CargoImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds, cargo.
     *
     * @return void
     */
    public function run()
    {
    	$datas = Excel::import(new CargoImport, 'database/seeds/cargos.xlsx', null, \Maatwebsite\Excel\Excel::XLSX);
    }
}
