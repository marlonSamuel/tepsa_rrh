<?php

use Illuminate\Database\Seeder;
use App\Carnet;

class CarnetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for($i=1; $i<=500; $i++){
        	$data = new Carnet;
        	$data->codigo = $i<10 ? '000'.$i:'00'.$i;
        	$data->save();
    	}

    }
}
