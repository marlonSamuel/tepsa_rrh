<?php

namespace App;
use App\Carnet;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class CarnetEmpleado extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'carnet_empleados';
    protected $fillable = [
    	'empleado_id',
    	'carnet_id'
    ];

    public function carnet(){
    	return $this->belongsTo(Carnet::class, 'carnet_id');
    }
}
