<?php

namespace App;

use App\AsignacionEmpleado;
use App\AsignacionDomo;
use App\PagoEmpleadoEventual;
use Illuminate\Database\Eloquent\Model;

class PlanillaEventual extends Model
{
    protected $connection = 'rrh';

    protected $table = 'planilla_eventuals';

    protected $fillable = [
    	'asignacion_empleado_id',
    	'buque',
    	'numero',
    	'inicio_descarga',
        'fecha',
    	'fin_descarga',
        'bono_turno'
    ];

    public function asignacion(){
    	return $this->hasMany(AsignacionEmpleado::class);
    }
    public function asignacion_domo(){
        return $this->hasMany(AsignacionDomo::class,'asignacion_empleado_id','asignacion_empleado_id');
    }

    public function pago_eventual(){
    	return $this->hasMany(PagoEmpleadoEventual::class);
    }
}
