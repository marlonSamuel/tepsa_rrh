<?php

namespace App;

use App\CargoTurno;
use App\DetalleAsignacionEmpleado;
use Illuminate\Database\Eloquent\Model;

class AsistenciaTurnoBodega extends Model
{
    protected $connection = 'rrh';

    protected $table = 'asistencia_turno_bodegas';

    protected $fillable = [
    	'detalle_asignacion_empleado_id',
    	'cargo_turno_id',
    	'hora_entrada',
    	'hora_salida',
    	'bodega'
    ];

    public function detalle_asignacion(){
    	$this->belongsTo(DetalleAsignacionEmpleado::class);
    }

    public function cargo_turno(){
    	$this->belongsTo(CargoTurno::class);
    }
}
