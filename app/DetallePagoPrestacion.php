<?php

namespace App;

use App\Prestacion;
use App\PagoEmpleadoEventual;
use Illuminate\Database\Eloquent\Model;

class DetallePagoPrestacion extends Model
{
    protected $connection = 'rrh';
    protected $table = 'detalle_pago_prestacions';

      protected $fillable = [
    	'pago_empleado_eventual_id',
    	'prestacion_id',
    	'total'
    ];

    public function prestacion(){
    	return $this->belongsTo(Prestacion::class);
    }

    public function pago(){
    	return $this->belongsTo(PagoEmpleadoEventual::class);
    }
}
