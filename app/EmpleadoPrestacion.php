<?php

namespace App;

use App\Prestacion;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class EmpleadoPrestacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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
