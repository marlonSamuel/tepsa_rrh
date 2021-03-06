<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Rol;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'empleado_id', 'email', 'password','rol_id'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rol(){
        return $this->belongsTo(Rol::class);
    }

    public function empleado(){
        return $this->belongsTo(Empleado::class,'empleado_id','idEmpleado');
    }
}
