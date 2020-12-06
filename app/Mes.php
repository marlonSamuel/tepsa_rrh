<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $connection = 'rrh';
    protected $table = 'meses';
    protected $fillable = [
        'id',
        'mes'
    ];
}
