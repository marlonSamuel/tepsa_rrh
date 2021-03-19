<?php

namespace App;

use App\Empleado;
use App\DetallePagoEmpleadoFijo;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class PagoEmpleadoDomo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'pago_empleado_domos';
    protected $fillable = [
    	'planilla_eventual_id',
    	'empleado_id',
    	'conteo_turno',
    	'total',
    	'confirmar_pago',
    ];

    public function empleado(){
        return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }
    public function planilla_eventual(){
        return $this->belongsTo(PlanillaEventual::class,'planilla_eventual_id','id');
    }

}
