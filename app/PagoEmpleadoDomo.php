<?php

namespace App;

use App\Empleado;
use App\DetallePagoEmpleadoFijo;
use Illuminate\Database\Eloquent\Model;

class PagoEmpleadoDomo extends Model
{
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
    public function detalle_pago(){
        return $this->hasMany(DetallePagoEmpleadoFijo::class);
    }

}
