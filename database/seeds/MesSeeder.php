<?php

use Illuminate\Database\Seeder;
use App\Mes;

class MesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mes = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
    	for($i=0; $i<=11; $i++){
        	$data = new Mes;
            $data->mes = $mes[$i];
        	$data->save();
    	}
    }
}
