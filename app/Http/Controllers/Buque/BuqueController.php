<?php

namespace App\Http\Controllers\Buque;

use App\Buque;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class BuqueController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $buques = Buque::where('estado','A')->get();
        return $this->showAll($buques);
    }
}
