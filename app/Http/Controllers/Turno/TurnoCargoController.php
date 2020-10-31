<?php

namespace App\Http\Controllers\Turno;

use App\Turno;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TurnoCargoController extends ApiController
{
    public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }
    public function index(Turno $turno){
        $cargos = $turno->cargo_turnos()->with('cargo')->get();
        return $this->showAll($cargos);
    }
}
