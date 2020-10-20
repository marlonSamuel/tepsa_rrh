<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsistenciaAlmuerzo extends Model
{
    protected $connection = ['rrh'];

    protected $table = ['asistencia_almuerzos'];

    protected $fillable = [
    	'detalle_asignacion_empleado_id'
    ];
}
