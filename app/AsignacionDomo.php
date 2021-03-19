<?php

namespace App;

use App\Empleado;
use App\Cargo;
use App\Carnet;
use App\AsistenciaDomo;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class AsignacionDomo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //

    protected $connection = 'rrh';

    protected $fillable = [
    	'asignacion_empleado_id',
    	'empleado_id',
    	'carnet_id',
    	'cargo_id',
    	'fecha'
    ];

    public function asignacion(){
    	return $this->belongsTo(AsignacionEmpleado::class,'asignacion_empleado_id');
    }

    public function empleado(){
    	return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }

    public function cargo(){
    	return $this->belongsTo(Cargo::class,'cargo_id','idCargo');
    }

    public function carnet(){
    	return $this->belongsTo(Carnet::class);
    }

    public function asistencia_domo()
    {
        return $this->hasMany(AsistenciaDomo::class);
    }
}
