<?php

namespace App;

use App\CargoTurno;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Turno extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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

