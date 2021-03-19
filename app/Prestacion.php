<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Prestacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'rrh';

    protected $table = 'prestacions';
    protected $fillable = [
        'descripcion',
        'fijo',
        'calculo',
        'debito_o_credito',
        'formula'
    ];
}