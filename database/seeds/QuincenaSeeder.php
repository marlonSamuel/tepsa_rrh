<?php

use Illuminate\Database\Seeder;
use App\Anio;
use App\Mes;
use App\Quincena;
use Carbon\Carbon;

class QuincenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $anio = Anio::where('anio', $now->year)->first();
        $meses = Mes::all();
        $cont = 0;
        $contQuincena = 1;
        foreach ($meses as $key => $value) {
            for ($i = 1; $i < 3; $i++) {
                $dateInicio = Carbon::createFromFormat('m/Y', $value->id . '/' . $anio->anio)->firstOfMonth();
                $dateFin = Carbon::createFromFormat('m/Y', $value->id . '/' . $anio->anio)->firstOfMonth();
                $inicio = $i == 1 ? $dateInicio : $dateInicio->addDays(15);
                $fin = $i == 1 ? $dateFin->addDay(14) : $dateFin->lastOfMonth();
                $data = new Quincena;
                $data->quincena = $contQuincena;
                $data->fecha_inicio = $inicio;
                $data->fecha_fin = $fin;
                $data->anio_id = $anio->id;
                $data->mes_id = $value->id;
                $data->cerrada = false;
                $data->fin_mes = $i == 1 ? false : true;
                $data->valor_hora_extra_simple = 0;
                $data->valor_hora_extra_doble = 0;
                $data->save();
                $contQuincena++;
            }
        }
    }
}
