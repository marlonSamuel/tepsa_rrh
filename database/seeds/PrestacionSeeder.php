<?php

use App\Empleado;
use App\Prestacion;
use App\EmpleadoPrestacion;
use Illuminate\Database\Seeder;

class PrestacionSeeder extends Seeder
{
    public function run()
    {
        $data = new Prestacion;
        $data->descripcion = 'bono 14';
        $data->fijo = 0;//es prestacion fija
        $data->debito_o_credito = 0; //es credito
        $data->calculo = 0;
        /* 
			formula para el calculo
        */
        $data->formula = '($pago->total_devengado*$pago->total_turnos)/365';
        $data->save();

        $data = new Prestacion;
        $data->descripcion = 'aguinaldo';
        $data->fijo = 0;//es prestacion fija
        $data->debito_o_credito = 0; //es credito
        /*formula*/
        $data->formula = '($pago->total_devengado*$pago->total_turnos)/365';
        $data->calculo = 0;
        $data->save();

        $data = new Prestacion;
        $data->descripcion = 'bonificacion incetivo';
        $data->fijo = 0;//es prestacion fija
        $data->debito_o_credito = 0; //es credito
        $data->calculo = 250;
        /**/
        $data->formula = '(($value->prestacion->calculo/30)/24)*8*$pago->total_turnos';
        $data->save();

        $data = new Prestacion;
        $data->descripcion = 'igss';
        $data->fijo = 1;//es prestacion fija
        $data->debito_o_credito = 1; //es debito
        $data->calculo = 0.0483;
        $data->save();

        $empleados = Empleado::all();

        $prestaciones = Prestacion::all();

        foreach ($empleados as $e) {
            foreach ($prestaciones as $p) {
                $data2 = new EmpleadoPrestacion;
                $data2->empleado_id = $e->idEmpleado;
                $data2->prestacion_id = $p->id;
                $data2->save();
            }
        }

    }
}
