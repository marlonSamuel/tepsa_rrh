<?php

namespace App;

use App\CargoTurno;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';

    protected $fillable = [
    	'hora_inicio',
    	'hora_fin',
    	'numero',
    	'doce_horas'
    ];

    public function cargo_turnos(){
    	return $this->hasMany(CargoTurno::class);
    }
}

