<?php

namespace App;

use App\Empleado;
use App\DetallePagoEventual;
use App\DetallePagoPrestacion;
use Illuminate\Database\Eloquent\Model;

class PagoEmpleadoEventual extends Model
{
    protected $connection = 'rrh';

    protected $table = 'pago_empleado_eventuals';
    protected $fillable = [
    	'planilla_eventual_id',
    	'empleado_id',
    	'alimentacion',
    	'prestamos',
    	'otros_descuentos',
    	'confirmar_pago',
        'septimo',
        'total_turnos',
        'total_monto_turnos',
        'total_prestaciones',
        'descuento_prestaciones',
        'bono_turnos',
        'total_devengado',
        'total_liquidado'
    ];

    public function detalle_pago(){
    	return $this->hasMany(DetallePagoEventual::class);
    }

    public function prestaciones(){
    	return $this->hasMany(DetallePagoPrestacion::class);
    }

    public function empleado(){
        return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }
}
