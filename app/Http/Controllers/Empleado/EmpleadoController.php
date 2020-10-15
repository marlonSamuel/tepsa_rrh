<?php

namespace App\Http\Controllers\Empleado;

use App\Empleado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class EmpleadoController extends ApiController
{
   public function __construct()
    {
        //parent::__construct(); //validacion de autenticacion
    }

    public function index()
    {
        $empleados = Empleado::with('cargo')->get();

        return $this->showAll($empleados);
    }
   /* public function store(Request $request)
    {
        $rules = [
            'dpi' => 'required|string|unique:planificacion.empleado',
            'nit' => 'required'
            'primer_nombre' => 'required|string',
            'primer_apellido' => 'required|string',
            //'tipo_empleado'=>'required'
        ];

        $this->validate($request, $rules);
        $data = $request->all();

        $empleado = Empleado::create($data);

        return $this->showOne($empleado, 201, 'insert');
    }*/
}
