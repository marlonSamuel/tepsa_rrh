<?php

namespace App\Imports;

use App\Cargo;
use App\Turno;
use App\CargoTurno;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CargoImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
    	$cargos = Cargo::all();

    	/* 
			'cargo_id',
	    	'turno_id',
	    	'salario',
	        'salario_hora'
    	*/

		foreach ($collection as $row) {
            $cargo = $cargos->where('nombre',$row[0])->first();

            if(!is_null($cargo)){
            	$insert = new CargoTurno();

            	$insert->cargo_id = $cargo->idCargo;
            	$insert->turno_id = 1;
            	$insert->salario = floatval($row[1]);
            	$insert->salario_hora= floatval($row[1])/8;
            	$insert->save();

            	$insert = new CargoTurno();
            	$insert->cargo_id = $cargo->idCargo;
            	$insert->turno_id = 2;
            	$insert->salario = floatval($row[2]);
            	$insert->salario_hora= floatval($row[2])/8;
            	$insert->save();

            	$insert = new CargoTurno();
            	$insert->cargo_id = $cargo->idCargo;
            	$insert->turno_id = 3;
            	$insert->salario = floatval($row[3]);
            	$insert->salario_hora= floatval($row[3])/8;
            	$insert->save();
            }   
        } 
     	  
    }
}
