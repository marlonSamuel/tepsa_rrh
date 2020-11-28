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
    	'bodega',
        'observaciones',
        'bloqueado',
        'desbloqueado',
        'razon_desbloqueo'
    ];

    public function detalle_asignacion(){
    	return $this->belongsTo(DetalleAsignacionEmpleado::class);
    }

    public function cargo_turno(){
    	return $this->belongsTo(CargoTurno::class,'cargo_turno_id');
    }
}
