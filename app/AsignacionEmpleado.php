<?php

namespace App;

use App\PlanoEstiba;
use App\DetalleAsignacionEmpleado;
use Illuminate\Database\Eloquent\Model;

class AsignacionEmpleado extends Model
{
    protected $connection = 'rrh';
    protected $table = 'asignacion_empleados';

    protected $fillable = [
    	'planificacion_id'
    ];

    public function planificacion(){
    	return $this->belongsTo(PlanoEstiba::class,'planificacion_id','idPlano_Estiba');
    }

    public function detalle_asignacion(){
    	return $this->hasMany(DetalleAsignacionEmpleado::class,'asignacion_empleado_id');
    }
}
