<?php
namespace App;

use App\DetallePagoEmpleadoFijo;
use App\Empleado;
use App\Quincena;

use Illuminate\Database\Eloquent\Model;

class PagoEmpleadoFijo extends Model{
    protected $connection = 'rrh';
    protected $table = 'pago_empleado_fijos';
    protected $fillable=[
        'id',
        'quincena_id',
        'empleado_id',
        'cargo_id',
        'total',
        'otro_descuento',
        'anticipo',
        'otro_ingreso',
        'hora_extra_simple',
        'hora_extra_doble'
    ];

    public function detalle_pago(){
    	return $this->hasMany(DetallePagoEmpleadoFijo::class);
    }

    public function empleado(){
        return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }
    public function quincena(){
        return $this->belongsTo(Quincena::class,'quincena_id','id');
    }
}
