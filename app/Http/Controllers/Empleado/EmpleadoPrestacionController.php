<?php

namespace App\Http\Controllers\Empleado;

use App\EmpleadoPrestacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class EmpleadoPrestacionController extends ApiController
{
    public function __construct()
    {
        parent::__construct(); //validacion de autenticacion
    }

   	public function store(Request $request){

		foreach ($request->prestaciones as $item) {
			$data = new EmpleadoPrestacion();
			$data->empleado_id = $request->empleado_id;
			$data->prestacion_id = $item;
    		$data->save();;
		}
    	
    	return  $this->showOne($data,201,'insert');

    }

     /**
     */
    public function destroy($id)
    {
		$empleado_prestacion = EmpleadoPrestacion::find($id);
		$empleado_prestacion->delete();

        return $this->showOne($empleado_prestacion,201,'delete');
    }

}