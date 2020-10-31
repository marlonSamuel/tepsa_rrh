<?php

namespace App;

use App\Empleado;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $connection = 'planificacion';

    protected $table = 'cargo';
    protected $primaryKey = 'idCargo';
    public $timestamps = false;

    protected $fillable = [
    	'idCargo',
    	'nombre',
    	'descripcion',
        'salario',
        'estado'
    ];

    public function empleados(){
    	return $this->hasMany(Empleado::class,'idCargo','idCargo');
    }
}
