<?php

namespace App;

use App\Prestacion;
use Illuminate\Database\Eloquent\Model;

class EmpleadoPrestacion extends Model
{
    protected $connection = 'rrh';
    protected $table = 'empleado_prestacions';

    protected $fillable = [
        'empleado_id',
        'prestacion_id',
    ];

    public function prestacion(){
    	return $this->belongsTo(Prestacion::class,'prestacion_id');
    }


}
