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
    	$planificacion = PlanoEstiba::where([['fecha_atraque',$date],['idBuque',$buque_id]])->with('buque','asignacion')->first();

    	if(is_null($planificacion)) return $this->errorResponse('no se encontró ningun importación con los datos especificados',404);

        if(!is_null($planificacion->asignacion)) return $this->errorResponse('importación con los datos especificados ya fue asignada',421);
    	
    	return $this->showOne($planificacion);
    }
}
