<?php

namespace App;

use App\Prestacion;
use App\PagoEmpleadoEventual;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class DetallePagoPrestacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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
