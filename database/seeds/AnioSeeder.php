<?php

use Illuminate\Database\Seeder;
use App\Anio;

class AnioSeeder extends Seeder
{
    public function run()
    {
        $start = 2020;
    	for($i=1; $i<=20; $i++){
        	$data = new Anio;
            $data->anio = $start++;
        	$data->save();
    	}
    }

}
