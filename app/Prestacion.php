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
<<<<<<< HEAD
        'debito_credito'
=======
        'debito_o_credito',
        'formula'
>>>>>>> 2e10fa034832a74abb335d60a8e9a38f37cd4d8f
    ];
}