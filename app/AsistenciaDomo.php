<?php

namespace App;

use App\AsistenciaDomo;
use Illuminate\Database\Eloquent\Model;

class AsistenciaDomo extends Model
{
    protected $connection = 'rrh';

    protected $table = 'asistencia_domos';

    protected $fillable = [
    	'asignacion_domo_id',
    	'hora_entrada',
    	'hora_salida',
    	'turno',
        'observaciones',
        'bloqueado',
        'desbloqueado',
        'razon_desbloqueo'
    ];

    public function asignacion_domo(){
    	return $this->belongsTo(AsignacionDomo::class);
    }
}
