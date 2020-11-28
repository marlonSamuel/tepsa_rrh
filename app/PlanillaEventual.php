<?php

namespace App;

use App\AsignacionEmpleado;
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
    	'fin_descarga'
    ];

    public function asignacion(){
    	return $this->hasMany(AsignacionEmpleado::class);
    }

    public function pago_eventual(){
    	return $this->hasMany(PagoEmpleadoEventual::class);
    }
}
