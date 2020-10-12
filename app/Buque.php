<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buque extends Model
{
    protected $connection = 'planificacion';

    protected $table = 'buque';

    protected $fillable = [
    	'idBuque',
    	'nombre',
    	'no_bodegas'
    ];
}
