<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Quincena extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';
    protected $table = 'quincenas';
    protected $fillable = [
        'quincena',
        'fecha_inicio',
        'fecha_fin',
        'anio_id',
        'mes_id',
        'cerrada',
        'fin_mes',       
        'valor_hora_extra_simple',
        'valor_hora_extra_doble',
    ];
    public function anio(){
    	return $this->belongsTo(Anio::class,'anio_id');
    }
    public function mes(){
    	return $this->belongsTo(Mes::class,'mes_id');
    }
    public function pago_empleado_fijo(){
        return $this->hasMany(PagoEmpleadoFijo::class);
    }
}
