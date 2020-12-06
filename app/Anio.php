<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anio extends Model
{
    protected $connection = 'rrh';
    protected $table = 'anios';
    protected $fillable = [
        'id',
        'anio'
    ];
}
