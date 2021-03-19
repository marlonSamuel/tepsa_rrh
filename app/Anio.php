<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Quincena;
use OwenIt\Auditing\Contracts\Auditable;
class Anio extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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
