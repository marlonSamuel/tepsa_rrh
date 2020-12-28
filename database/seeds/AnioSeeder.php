<?php

use Illuminate\Database\Seeder;
use App\Anio;

class AnioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = 2019;
    	for($i=1; $i<=20; $i++){
        	$data = new Anio;
            $data->anio = $start++;
        	$data->save();
    	}
    }
}
