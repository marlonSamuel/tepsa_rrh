<?php

use App\Carnet;
use App\Empleado;
use Carbon\Carbon;
use App\CargoTurno;
use App\AsignacionEmpleado;
use App\AsistenciaAlmuerzo;
use App\AsistenciaTurnoBodega;
use Illuminate\Database\Seeder;
use App\DetalleAsignacionEmpleado;

class AsignacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $planificaciones = [84,85,86];

        foreach ($planificaciones as $p) {
        	$plan = new AsignacionEmpleado;
        	$plan->planificacion_id = $p;
        	$plan->save();

        	/* 
				'turno_id',
		    	'asignacion_empleado_id',
		    	'empleado_id',
		    	'carnet_id',
		        'fecha'
        	*/

		    
		    #recorrer de 1  a 8 fechas
		    for ($k=1; $k <= rand(1,8); $k++) { 
		    	#recorrer turnos
		    	for ($i=1; $i <=3 ; $i++) { 
		    		#asignar de 8 a 20 empleados por fecha
		    		for ($j=1; $j<=rand(8,20); $j++) { 
			    		$detalle = new DetalleAsignacionEmpleado;
				    	$detalle->turno_id = $i;
				    	$detalle->asignacion_empleado_id = $plan->id;
				    	$detalle->empleado_id = Empleado::all()->random(1)->first()->idEmpleado;

				    	$detalle->carnet_id = rand(1,500);
				    	$detalle->fecha = Carbon::parse($plan->fecha_atraque)->add($k,'day');
				    	$detalle->save();


				    	$cargo_t_ant = DetalleAsignacionEmpleado::where('empleado_id',$detalle->empleado_id)->with('asistencia_turno.cargo_turno.turno')->first();

				    	if(!is_null($cargo_t_ant) && !is_null($cargo_t_ant->asistencia_turno)){
				    		$turno = $cargo_t_ant->asistencia_turno->cargo_turno;
				    	}else{
				    		$turno = CargoTurno::where('turno_id',$i)->with('turno')->get()->random();
				    	}
					    
					    $extra = Carbon::parse($detalle->fecha)->format('Y-m-d');

					    $asistencia_turno = new AsistenciaTurnoBodega;
					    $asistencia_turno->detalle_asignacion_empleado_id = $detalle->id;
					    $asistencia_turno->cargo_turno_id = $turno->id;
					    $asistencia_turno->hora_entrada = Carbon::create($extra.' '.(string)$turno->hora_inicio);
					    $asistencia_turno->hora_salida = Carbon::create($extra.' '.(string)$turno->hora_fin);
					    $asistencia_turno->bodega = rand(1,30);
					    $asistencia_turno->save();

					    $asistencia_almuerzo = new AsistenciaAlmuerzo;
					    $asistencia_almuerzo->detalle_asignacion_empleado_id = $detalle->id;
					    $asistencia_almuerzo->save();
			    	}
		    	}
		    	   
		    }

        }
    }
}
