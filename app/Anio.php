<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quincena;

class Anio extends Model
{
    protected $connection = 'rrh';
    protected $table = 'anios';
    protected $fillable = [
        'id',
        'anio'
    ];

    public function quincenas(){
    	return $this->hasMany(Quincena::class);
    }
}
