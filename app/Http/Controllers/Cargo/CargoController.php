<?php

namespace App\Http\Controllers\Cargo;

use App\Cargo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CargoController extends ApiController
{
   public function __construct()
    {
        #parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $empleados = Cargo::with('empleados')->get();
        return $this->showAll($empleados);
    }
}
