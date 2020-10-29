<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoTurno extends Model
{
    protected $connection = 'rrh';

    protected $table = 'cargo_turnos';

    protected $fillable = [
    	'cargo_id',
    	'turno_id',
    	'salario'
    ];

    public function cargo(){
    	return $this->belongsTo(Cargo::class,'cargo_id','idCargo');
    }

    public function turno(){
    	return $this->belongsTo(Turno::class);
    }
}
