<?php

namespace App;

use App\PlanoEstiba;
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
}
