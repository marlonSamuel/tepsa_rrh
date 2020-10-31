<?php

use Illuminate\Database\Seeder;
use App\Turno;

class TurnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new Turno;
        $data->hora_inicio = '07:00';
        $data->hora_fin = '14:30';
        $data->numero = 1;
        $data->save();

        $data = new Turno;
        $data->hora_inicio = '14:30';
        $data->hora_fin = '21:30';
        $data->numero = 2;
        $data->save();

        $data = new Turno;
        $data->hora_inicio = '21:30';
        $data->hora_fin = '07:00';
        $data->numero = 3;
        $data->save();
    }
}
