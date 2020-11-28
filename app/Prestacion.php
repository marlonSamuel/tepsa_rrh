<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prestacion extends Model
{
    protected $connection = 'rrh';

    protected $table = 'prestacions';
    protected $fillable = [
        'descripcion',
        'fijo',
        'calculo',
        'debito_credito'
    ];
}