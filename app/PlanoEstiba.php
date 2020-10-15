<?php

namespace App;

use App\Buque;
use App\AsignacionEmpleado;
use Illuminate\Database\Eloquent\Model;

class PlanoEstiba extends Model
{
    protected $connection = 'planificacion';

    protected $table = 'plano_estiba';

    protected $fillable = [
    	'idPlano_Estiba',
    	'no_importacion',
    	'peso_total',
    	'idBuque',
    	'fecha_atracke'
    ];

    public function buque(){
    	return $this->belongsTo(Buque::class,'idBuque','idBuque');
    }

    public function asignacion(){
        return $this->hasOne(AsignacionEmpleado::class,'planificacion_id','idPlano_Estiba');
    }
}
