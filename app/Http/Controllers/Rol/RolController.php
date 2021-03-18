<?php

namespace App\Http\Controllers\Rol;

use App\Rol;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class RolController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    /**
     * get all roles
     */
    public function index()
    {
        $roles = Rol::all();
        return $this->showAll($roles);
    }
}
