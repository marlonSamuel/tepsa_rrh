<?php

namespace App;

use App\PlanoEstiba;
use App\DetalleAsignacionEmpleado;
use App\AsignacionDomo;
use Illuminate\Database\Eloquent\Model;

class AsignacionEmpleado extends Model
{
    protected $connection = 'rrh';
    protected $table = 'asignacion_empleados';

    protected $fillable = [
    	'planificacion_id',
        'terminada'
    ];

    public function planificacion(){
    	return $this->belongsTo(PlanoEstiba::class,'planificacion_id','idPlano_Estiba');
    }

    public function detalle_asignacion(){
    	return $this->hasMany(DetalleAsignacionEmpleado::class,'asignacion_empleado_id');
    }

    public function asignacion_domos(){
        return $this->hasMany(AsignacionDomo::class,'asignacion_empleado_id');
    }

    public function planilla_eventual(){
        return $this->hasOne(PlanillaEventual::class,'asignacion_empleado_id');
    }
}
