<?php

namespace App;

use App\Turno;
use App\Carnet;
use App\Empleado;
use Carbon\Carbon;
use App\AsignacionEmpleado;
use App\AsistenciaAlmuerzo;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class DetalleAsignacionEmpleado extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'detalle_asignacion_empleados';

    protected $fillable = [
    	'turno_id',
    	'asignacion_empleado_id',
    	'empleado_id',
    	'carnet_id',
        'fecha'
    ];

    public function turno(){
    	return $this->belongsTo(Turno::class);
    }

    public function asignacion(){
    	return $this->belongsTo(AsignacionEmpleado::class,'asignacion_empleado_id');
    }

    public function empleado(){
    	return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }

    public function carnet(){
    	return $this->belongsTo(Carnet::class);
    }

    //verificar asistencia turno, fecha actual
    public function asistencia_turno(){
        //$today = Carbon::now('America/Guatemala');
        //$today = Carbon::now()->format('Y-m-d');
        return $this->hasOne(AsistenciaTurnoBodega::class);
    }

    public function asistencia_almuerzo(){
        return $this->hasMany(AsistenciaAlmuerzo::class);
    }
}
