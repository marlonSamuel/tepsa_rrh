<?php

namespace App\Http\Controllers\Cargo;

use App\Cargo;
use App\CargoTurno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CargoTurnoController extends ApiController
{
   public function __construct()
    {
        #parent::__construct(); //validacion de autenticacion
    }

    public function index(Cargo $cargo)
    {
        $turnos = $cargo->cargo_turnos()->with('turno','cargo')->get();
        return $turnos;
    }

}
