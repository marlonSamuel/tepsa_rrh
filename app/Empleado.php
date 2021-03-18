<?php

namespace App;

use App\Cargo;
use App\EmpleadoPrestacion;
use App\CarnetEmpleado;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    protected $connection = 'planificacion';

    protected $table = 'empleado';
    protected $primaryKey = 'idEmpleado';
    public $timestamps = false;

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
        'tipo_empleado',
        'estado',
        'igss',
        'fecha_ingreso'
    ];

    public function Cargo()
    {
        return $this->belongsTo(Cargo::class, 'idCargo', 'idCargo');
    }
    public function empleado_prestacion()
    {
        return $this->hasMany(EmpleadoPrestacion::class, 'empleado_id', 'idEmpleado');
    }

    public function carnet_empleado()
    {
        return $this->belongsTo(CarnetEmpleado::class, 'empleado_id', 'idEmpleado');
    }
}