<?php
namespace App;
use App\Prestacion;
use App\PagoEmpleadoFijo;
use Illuminate\Database\Eloquent\Model;

class DetallePagoEmpleadoFijo extends Model{
	protected $connection = 'rrh';
    protected $table = 'detalle_pago_empleado_fijos';

    protected $fillable = [
    	'pago_empleado_fijo_id',
    	'prestacion_id',
    	'total'
    ];

    public function prestacion(){
    	return $this->belongsTo(Prestacion::class);
    }

    public function pago(){
    	return $this->belongsTo(PagoEmpleadoFijo::class);
    }

}