<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class CargoTurno extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'cargo_turnos';

    protected $fillable = [
    	'cargo_id',
    	'turno_id',
    	'salario',
        'salario_hora'
    ];

    public function cargo(){
    	return $this->belongsTo(Cargo::class,'cargo_id','idCargo');
    }

    public function turno(){
    	return $this->belongsTo(Turno::class);
    }
}
