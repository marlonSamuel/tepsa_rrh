<?php

namespace App;

use App\Empleado;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $connection = 'planificacion';

    protected $table = 'cargo';

    protected $fillable = [
    	'idCargo',
    	'nombre',
    	'descripcion'
    ];

    public function empleados(){
    	return $this->hasMany(Empleado::class,'idCargo','idCargo');
    }
}
