<?php

use Illuminate\Database\Seeder;
use App\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new Rol;
        $data->nombre = "administrador";
        $data->save();

        $data = new Rol;
        $data->nombre = "asistencia muelle";
        $data->save();

        $data = new Rol;
        $data->nombre = "asistencia domo";
        $data->save();

        $data = new Rol;
        $data->nombre = "asistencia alimentos";
        $data->save();
    }
}
