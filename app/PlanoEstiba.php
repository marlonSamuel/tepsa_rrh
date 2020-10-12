<?php

namespace App;

use App\Buque;
use Illuminate\Database\Eloquent\Model;

class PlanoEstiba extends Model
{
    protected $connection = 'planificacion';

    protected $table = 'PlanoEstiba';

    protected $fillable = [
    	'idPlano_Estiba',
    	'no_importacion',
    	'peso_total',
    	'idBuque',
    	'fecha_atracke'
    ];

    public function buque(){
    	return $this->belongsTo(Buque::class,'idBuque');
    }
}
