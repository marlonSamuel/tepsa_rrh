<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carnet extends Model
{
    protected $connection = 'rrh';

    protected $table = 'carnets';
    protected $fillable = [
    	'codigo',
    	'asignado'
    ];
}
