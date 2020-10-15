<?php

namespace App;

use App\Turno;
use App\Carnet;
use App\Empleado;
use App\AsignacionEmpleado;
use Illuminate\Database\Eloquent\Model;

class DetalleAsignacionEmpleado extends Model
{
    protected $connection = 'rrh';

    protected $table = 'detalle_asignacion_empleados';

    protected $fillable = [
    	'turno_id',
    	'asignacion_empleado_id',
    	'empleado_id',
    	'carnet_id'
    ];

    public function turno(){
    	return $this->belongsTo(Turno::class);
    }

    public function asignacion(){
    	return $this->belongsTo(AsignacionEmpleado::class,'asignacion_empleado_id');
    }

    public function empleado(){
    	return $this->belongsTo(Empleado::class,'idEmpleado','empleado_id');
    }

    public function carnet(){
    	return $this->belongsTo(Carnet::class);
    }
}
