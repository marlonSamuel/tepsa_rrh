<?php

namespace App;
use App\Carnet;
use Illuminate\Database\Eloquent\Model;

class CarnetEmpleado extends Model
{
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
