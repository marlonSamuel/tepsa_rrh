<?php

namespace App;

use App\CargoTurno;
use App\Prestacion;
use App\PagoEmpleadoEventual;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class DetallePagoEventual extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';
    protected $table = 'detalle_pago_eventuals';

    protected $fillable = [
    	'cargo_turno_id',
    	'pago_empleado_eventual_id',
    	'conteo_turnos',
    	'valor_turno',
        'bono_turno',
    	'total'
    ];

    public function asistencia_turno(){
    	return $this->belongsTo(Prestacion::class);
    }

    public function pago(){
    	return $this->belongsTo(PagoEmpleadoEventual::class);
    }

    public function cargo_turno(){
        return $this->belongsTo(CargoTurno::class);
    }
}
