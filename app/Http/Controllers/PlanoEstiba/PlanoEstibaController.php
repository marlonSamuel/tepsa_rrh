<?php

namespace App\Http\Controllers\PlanoEstiba;

use App\PlanoEstiba;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class PlanoEstibaController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $planificaciones = PlanoEstiba::with('buque')->get();
        return $this->showAll($planificaciones);
    }

    public function search($date,$buque_id)
    {
    	$planificacion = PlanoEstiba::where([['fecha_atraque',$date],['idBuque',$buque_id]])->with('buque')->first();

    	if(is_null($planificacion)) return $this->errorResponse('no se encontro ninguan importación con los datos especificados',404);
    	
    	return $this->showOne($planificacion);
    }
}
