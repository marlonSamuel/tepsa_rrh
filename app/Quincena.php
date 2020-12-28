<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quincena extends Model
{
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
