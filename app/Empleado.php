<?php

namespace App;

use App\Cargo;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    protected $connection = 'planificacion';
    //dd($connection);
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
        'cuenta',
        //'tipo_empleado',
        'foto'
    ];

    public function Cargo(){
    	return $this->belongsTo(Cargo::class,'idCargo','idCargo');
    }
}
