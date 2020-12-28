<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quincena;

class Mes extends Model
{
    protected $connection = 'rrh';
    protected $table = 'meses';
    protected $fillable = [
        'id',
        'mes'
    ];

    public function quincenas(){
    	return $this->hasMany(Quincena::class);
    }
}
