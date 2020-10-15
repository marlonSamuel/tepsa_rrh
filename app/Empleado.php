<?php

namespace App;

use App\Cargo;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    protected $connection = 'planificacion';
    
    protected $table = 'empleado';

    protected $fillable = [
    	'idEmpleado',
    	'nit',
    	'dpi',
    	'primer_nombre',
    	'segundo_nombre',
    	'primer_apellido',
    	'segundo_apellido',
    	'direccion',
    	'telefono',
    	'idCargo',
        'foto',
        'cuenta',
        'tipo_empleado'
    ];

    public function Cargo(){
    	return $this->belongsTo(Cargo::class,'idCargo','idCargo');
    }
}
