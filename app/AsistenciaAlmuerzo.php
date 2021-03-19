<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class AsistenciaAlmuerzo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'asistencia_almuerzos';

    protected $fillable = [
    	'detalle_asignacion_empleado_id',
    	'tipo_alimento'
    ];
}
